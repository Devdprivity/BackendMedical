const venom = require('venom-bot');
const express = require('express');
const bodyParser = require('body-parser');
const axios = require('axios');
const cors = require('cors');
const morgan = require('morgan');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(morgan('dev'));

// Estado global
let venomClient = null;
let isReady = false;
let qrCode = null;

// Cola de mensajes en memoria
const messageQueue = [];
let isProcessingQueue = false;

// Límites de rate limiting
const RATE_LIMITS = {
    messagesPerMinute: 3,        // 3 mensajes por minuto
    messagesPerHour: 15,         // 15 mensajes por hora
    messagesPerDay: 100,         // 100 mensajes por día
    delayBetweenMessages: 3000,  // 3 segundos mínimo
    randomDelayRange: 2000       // +/- 2 segundos aleatorios
};

// Contadores de mensajes
const counters = {
    lastMinute: { count: 0, resetAt: Date.now() + 60000 },
    lastHour: { count: 0, resetAt: Date.now() + 3600000 },
    lastDay: { count: 0, resetAt: Date.now() + 86400000 }
};

/**
 * Inicializar cliente Venom Bot
 */
async function initializeVenom() {
    try {
        console.log('🚀 Inicializando Venom Bot...');

        venomClient = await venom.create(
            {
                session: 'backendmedical-session',
                multidevice: true,
                headless: 'new',
                useChrome: true,
                debug: false,
                logQR: true,
                browserArgs: [
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-accelerated-2d-canvas',
                    '--no-first-run',
                    '--no-zygote',
                    '--disable-gpu'
                ],
            },
            // QR Code callback
            (base64Qr, asciiQR) => {
                console.log('📱 Escanea el código QR con WhatsApp:');
                console.log(asciiQR);
                qrCode = base64Qr;
            },
            // Status callback
            (statusSession, session) => {
                console.log('📊 Estado de sesión:', statusSession);
                if (statusSession === 'qrReadSuccess' || statusSession === 'isLogged') {
                    qrCode = null;
                }
            }
        );

        // Configurar listeners de eventos
        setupEventListeners();

        isReady = true;
        console.log('✅ Venom Bot iniciado correctamente');

        // Iniciar procesamiento de cola
        processMessageQueue();

    } catch (error) {
        console.error('❌ Error al inicializar Venom Bot:', error);
        setTimeout(initializeVenom, 10000); // Reintentar en 10 segundos
    }
}

/**
 * Configurar listeners de eventos
 */
function setupEventListeners() {
    // Mensaje entrante
    venomClient.onMessage(async (message) => {
        try {
            console.log('📨 Mensaje recibido:', {
                from: message.from,
                body: message.body,
                timestamp: message.timestamp
            });

            // Enviar al backend Laravel
            await notifyLaravel('incoming', {
                from: message.from,
                body: message.body,
                timestamp: message.timestamp,
                isGroup: message.isGroupMsg,
                sender: message.sender,
                chatId: message.chatId
            });

        } catch (error) {
            console.error('❌ Error al procesar mensaje entrante:', error);
        }
    });

    // Estado de mensaje
    venomClient.onAck(async (ack) => {
        console.log('✅ ACK recibido:', ack);

        // Notificar a Laravel del estado
        await notifyLaravel('ack', {
            id: ack.id._serialized,
            ack: ack.ack, // 1: enviado, 2: recibido, 3: leído
            timestamp: ack.t
        });
    });
}

/**
 * Notificar al backend Laravel
 */
async function notifyLaravel(event, data) {
    try {
        await axios.post(`${process.env.LARAVEL_API_URL}/api/whatsapp/webhook/${event}`, data, {
            headers: {
                'Authorization': `Bearer ${process.env.LARAVEL_API_TOKEN}`,
                'Content-Type': 'application/json'
            },
            timeout: 5000
        });
    } catch (error) {
        console.error(`❌ Error al notificar a Laravel (${event}):`, error.message);
    }
}

/**
 * Verificar límites de rate limiting
 */
function checkRateLimits() {
    const now = Date.now();

    // Reset contadores si es necesario
    if (now > counters.lastMinute.resetAt) {
        counters.lastMinute = { count: 0, resetAt: now + 60000 };
    }
    if (now > counters.lastHour.resetAt) {
        counters.lastHour = { count: 0, resetAt: now + 3600000 };
    }
    if (now > counters.lastDay.resetAt) {
        counters.lastDay = { count: 0, resetAt: now + 86400000 };
    }

    // Verificar límites
    if (counters.lastMinute.count >= RATE_LIMITS.messagesPerMinute) {
        return { allowed: false, reason: 'Límite por minuto alcanzado', retryAfter: counters.lastMinute.resetAt - now };
    }
    if (counters.lastHour.count >= RATE_LIMITS.messagesPerHour) {
        return { allowed: false, reason: 'Límite por hora alcanzado', retryAfter: counters.lastHour.resetAt - now };
    }
    if (counters.lastDay.count >= RATE_LIMITS.messagesPerDay) {
        return { allowed: false, reason: 'Límite diario alcanzado', retryAfter: counters.lastDay.resetAt - now };
    }

    return { allowed: true };
}

/**
 * Incrementar contadores
 */
function incrementCounters() {
    counters.lastMinute.count++;
    counters.lastHour.count++;
    counters.lastDay.count++;
}

/**
 * Procesar cola de mensajes
 */
async function processMessageQueue() {
    if (isProcessingQueue || messageQueue.length === 0 || !isReady) {
        setTimeout(processMessageQueue, 1000);
        return;
    }

    isProcessingQueue = true;

    try {
        const job = messageQueue.shift();

        // Verificar rate limits
        const rateLimitCheck = checkRateLimits();
        if (!rateLimitCheck.allowed) {
            console.log(`⏸️  Rate limit: ${rateLimitCheck.reason}. Reintentando en ${Math.ceil(rateLimitCheck.retryAfter / 1000)}s`);

            // Volver a encolar
            messageQueue.unshift(job);

            // Esperar y reintentar
            setTimeout(() => {
                isProcessingQueue = false;
                processMessageQueue();
            }, rateLimitCheck.retryAfter);
            return;
        }

        // Delay humano entre mensajes
        const delay = RATE_LIMITS.delayBetweenMessages + Math.random() * RATE_LIMITS.randomDelayRange;
        await new Promise(resolve => setTimeout(resolve, delay));

        // Enviar mensaje
        console.log(`📤 Enviando mensaje a ${job.to}...`);

        const formattedNumber = formatPhoneNumber(job.to);
        const result = await venomClient.sendText(formattedNumber, job.message);

        incrementCounters();

        // Notificar a Laravel que se envió
        await notifyLaravel('sent', {
            message_id: job.messageId,
            whatsapp_message_id: result.id._serialized,
            status: 'sent',
            sent_at: new Date().toISOString()
        });

        console.log(`✅ Mensaje enviado exitosamente a ${job.to}`);

    } catch (error) {
        console.error('❌ Error al enviar mensaje:', error);

        // Notificar a Laravel del error
        if (messageQueue[0]) {
            await notifyLaravel('failed', {
                message_id: messageQueue[0].messageId,
                status: 'failed',
                error: error.message
            });
        }
    } finally {
        isProcessingQueue = false;

        // Continuar procesando cola
        setTimeout(processMessageQueue, 1000);
    }
}

/**
 * Formatear número de teléfono para WhatsApp
 */
function formatPhoneNumber(phone) {
    // Remover caracteres no numéricos
    let cleaned = phone.replace(/\D/g, '');

    // Si no tiene código de país, asumir Perú (+51)
    if (!cleaned.startsWith('51') && cleaned.length === 9) {
        cleaned = '51' + cleaned;
    }

    return cleaned + '@c.us';
}

// ============================================================================
// API ENDPOINTS
// ============================================================================

/**
 * GET / - Health check
 */
app.get('/', (req, res) => {
    res.json({
        service: 'BackendMedical WhatsApp Service',
        version: '1.0.0',
        status: isReady ? 'ready' : 'initializing',
        qrCode: qrCode ? 'available' : null
    });
});

/**
 * GET /status - Estado del servicio
 */
app.get('/status', async (req, res) => {
    try {
        let connectionStatus = 'disconnected';
        let phoneNumber = null;

        if (venomClient && isReady) {
            const state = await venomClient.getConnectionState();
            connectionStatus = state;

            try {
                const hostDevice = await venomClient.getHostDevice();
                phoneNumber = hostDevice.id.user;
            } catch (e) {
                console.error('Error al obtener número:', e.message);
            }
        }

        res.json({
            status: isReady ? 'ready' : 'initializing',
            connection: connectionStatus,
            phoneNumber,
            qrCode: qrCode ? `data:image/png;base64,${qrCode}` : null,
            queueLength: messageQueue.length,
            rateLimits: {
                lastMinute: `${counters.lastMinute.count}/${RATE_LIMITS.messagesPerMinute}`,
                lastHour: `${counters.lastHour.count}/${RATE_LIMITS.messagesPerHour}`,
                lastDay: `${counters.lastDay.count}/${RATE_LIMITS.messagesPerDay}`
            }
        });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

/**
 * GET /qr - Obtener código QR
 */
app.get('/qr', (req, res) => {
    if (!qrCode) {
        return res.status(404).json({ error: 'QR Code no disponible. La sesión puede estar ya autenticada.' });
    }

    res.json({
        qr: `data:image/png;base64,${qrCode}`,
        message: 'Escanea este código con WhatsApp'
    });
});

/**
 * POST /send - Enviar mensaje
 */
app.post('/send', (req, res) => {
    const { to, message, messageId } = req.body;

    if (!to || !message) {
        return res.status(400).json({ error: 'Los campos "to" y "message" son requeridos' });
    }

    if (!isReady) {
        return res.status(503).json({ error: 'El servicio WhatsApp no está listo. Intenta más tarde.' });
    }

    // Agregar a la cola
    messageQueue.push({
        to,
        message,
        messageId: messageId || Date.now(),
        queuedAt: new Date().toISOString()
    });

    res.json({
        success: true,
        message: 'Mensaje encolado',
        queuePosition: messageQueue.length,
        messageId: messageId || Date.now()
    });
});

/**
 * POST /send-bulk - Enviar múltiples mensajes
 */
app.post('/send-bulk', (req, res) => {
    const { messages } = req.body;

    if (!Array.isArray(messages) || messages.length === 0) {
        return res.status(400).json({ error: 'El campo "messages" debe ser un array no vacío' });
    }

    if (!isReady) {
        return res.status(503).json({ error: 'El servicio WhatsApp no está listo' });
    }

    // Validar mensajes
    const validMessages = messages.filter(msg => msg.to && msg.message);

    if (validMessages.length === 0) {
        return res.status(400).json({ error: 'No hay mensajes válidos en el array' });
    }

    // Agregar a la cola
    validMessages.forEach(msg => {
        messageQueue.push({
            to: msg.to,
            message: msg.message,
            messageId: msg.messageId || Date.now() + Math.random(),
            queuedAt: new Date().toISOString()
        });
    });

    res.json({
        success: true,
        message: `${validMessages.length} mensajes encolados`,
        totalQueued: messageQueue.length
    });
});

/**
 * GET /queue - Ver cola de mensajes
 */
app.get('/queue', (req, res) => {
    res.json({
        queueLength: messageQueue.length,
        messages: messageQueue.slice(0, 10), // Mostrar solo los primeros 10
        isProcessing: isProcessingQueue
    });
});

/**
 * POST /logout - Cerrar sesión
 */
app.post('/logout', async (req, res) => {
    try {
        if (venomClient) {
            await venomClient.logout();
            venomClient = null;
            isReady = false;
            qrCode = null;
        }

        res.json({ success: true, message: 'Sesión cerrada' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// ============================================================================
// INICIAR SERVIDOR
// ============================================================================

app.listen(PORT, () => {
    console.log(`🚀 WhatsApp Service escuchando en puerto ${PORT}`);
    console.log(`📊 Dashboard: http://localhost:${PORT}/status`);

    // Inicializar Venom Bot
    initializeVenom();
});

// Manejo de errores no capturados
process.on('unhandledRejection', (error) => {
    console.error('❌ Unhandled Rejection:', error);
});

process.on('uncaughtException', (error) => {
    console.error('❌ Uncaught Exception:', error);
});
