# BackendMedical WhatsApp Service

Microservicio Node.js con **Venom Bot** para envío automático de mensajes WhatsApp.

## 🚀 Instalación

```bash
cd whatsapp-service
npm install
```

## ⚙️ Configuración

1. Copia el archivo `.env.example` a `.env`:
```bash
cp .env.example .env
```

2. Edita `.env` y configura:
```env
PORT=3000
LARAVEL_API_URL=http://localhost:8000
LARAVEL_API_TOKEN=tu-token-secreto
```

## 🎯 Iniciar Servicio

### Desarrollo (con auto-reload):
```bash
npm run dev
```

### Producción:
```bash
npm start
```

## 📱 Autenticación WhatsApp

1. Inicia el servicio
2. Visita `http://localhost:3000/qr`
3. Escanea el código QR con WhatsApp
4. Espera la confirmación

## 📡 API Endpoints

### GET /status
Ver estado del servicio y código QR
```bash
curl http://localhost:3000/status
```

### POST /send
Enviar un mensaje
```bash
curl -X POST http://localhost:3000/send \
  -H "Content-Type: application/json" \
  -d '{
    "to": "51987654321",
    "message": "Hola desde Venom Bot!",
    "messageId": "123456"
  }'
```

### POST /send-bulk
Enviar múltiples mensajes
```bash
curl -X POST http://localhost:3000/send-bulk \
  -H "Content-Type: application/json" \
  -d '{
    "messages": [
      {"to": "51987654321", "message": "Mensaje 1"},
      {"to": "51998765432", "message": "Mensaje 2"}
    ]
  }'
```

### GET /queue
Ver cola de mensajes pendientes
```bash
curl http://localhost:3000/queue
```

## 🛡️ Rate Limiting

El servicio implementa límites automáticos:
- **Por minuto:** 3 mensajes
- **Por hora:** 15 mensajes
- **Por día:** 100 mensajes

## 📊 Monitoreo

Dashboard en tiempo real:
```
http://localhost:3000/status
```

## 🔧 Solución de Problemas

### El QR no aparece
- Verifica que el servicio esté corriendo
- Elimina la carpeta `tokens/` y reinicia

### Mensajes no se envían
- Verifica los rate limits en `/status`
- Revisa la cola en `/queue`
- Verifica que el número tenga formato: `51987654321` (Perú)

### Sesión cerrada
- Visita `/qr` y vuelve a escanear
- Verifica que WhatsApp no esté desconectado

## 📝 Logs

El servicio muestra logs detallados:
- 📨 Mensajes recibidos
- 📤 Mensajes enviados
- ⏸️ Rate limiting
- ❌ Errores

## 🔒 Seguridad

- Solo acepta conexiones desde Laravel con token válido
- Implementa rate limiting estricto
- Delays aleatorios para simular comportamiento humano
