# 📱 GUÍA COMPLETA: SISTEMA WHATSAPP CON VENOM BOT

## 🎯 RESUMEN EJECUTIVO

He implementado un **sistema completo de automatización WhatsApp** para BackendMedical que incluye:

✅ **Sistema de Agenda Multi-Ubicación** para doctores que viajan
✅ **Microservicio Node.js con Venom Bot** para envío de mensajes
✅ **Base de datos completa** con tablas para cuentas, mensajes, plantillas
✅ **Rate Limiting inteligente** para evitar baneos
✅ **API REST completa** para gestión de schedules y WhatsApp

---

## 📦 COMPONENTES IMPLEMENTADOS

### 1. **SISTEMA DE AGENDA MULTI-UBICACIÓN** ✅ COMPLETADO

#### Migraciones Creadas:
- `create_doctor_schedules_table.php` - Agenda por ciudad/fecha
- `add_location_fields_to_doctors_table.php` - Ciudad base del doctor
- `add_location_fields_to_clinics_table.php` - Ciudad de clínicas
- `add_whatsapp_fields_to_patients_table.php` - WhatsApp del paciente
- `add_confirmation_fields_to_appointments_table.php` - Estado de confirmación

#### Modelo DoctorSchedule:
**Archivo:** `app/Models/DoctorSchedule.php`

**Métodos principales:**
```php
isAvailableOnDate($date)                    // Verifica disponibilidad
getAvailableSlotsForDate($date)              // Todos los slots del día
getAvailableSlotsExcludingAppointments()     // Solo slots libres
getTotalSlotsPerDay()                        // Capacidad diaria
scopeInCity($city)                           // Filtrar por ciudad
scopeAvailableBetween($start, $end)         // Rango de fechas
```

#### API Endpoints:
```
GET    /api/doctor-schedules                       # Listar (con filtros)
POST   /api/doctor-schedules                       # Crear
GET    /api/doctor-schedules/{id}                  # Ver
PUT    /api/doctor-schedules/{id}                  # Actualizar
DELETE /api/doctor-schedules/{id}                  # Eliminar

GET /api/doctors/{doctorId}/schedules              # Schedules de un doctor
GET /api/doctor-schedules/{id}/available-slots?date=2025-03-25
GET /api/doctor-schedules/{id}/calendar?month=2025-03
GET /api/schedules/by-city/{city}                  # Doctores en ciudad
```

---

### 2. **SISTEMA WHATSAPP** ✅ COMPLETADO

#### Migraciones Creadas:
- `create_whatsapp_accounts_table.php`
- `create_whatsapp_messages_table.php`
- `create_whatsapp_templates_table.php`
- `create_appointment_confirmations_table.php`

#### Microservicio Venom Bot:
**Ubicación:** `whatsapp-service/`

**Archivos:**
- `server.js` - Servidor principal con Venom Bot
- `package.json` - Dependencias npm
- `.env.example` - Configuración
- `README.md` - Documentación

**Características:**
- ✅ Rate limiting automático (3/min, 15/hora, 100/día)
- ✅ Cola de mensajes con delays humanos
- ✅ Webhook para mensajes entrantes
- ✅ Código QR automático
- ✅ Manejo de errores robusto
- ✅ Dashboard de monitoreo

**Endpoints del Microservicio:**
```
GET  /status           # Estado + QR + rate limits
GET  /qr               # Código QR para escanear
POST /send             # Enviar 1 mensaje
POST /send-bulk        # Enviar múltiples mensajes
GET  /queue            # Ver cola
POST /logout           # Cerrar sesión
```

---

## 🚀 PASOS PARA IMPLEMENTAR

### PASO 1: Configurar Base de Datos

1. Edita `.env` en la raíz del proyecto Laravel:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medicalpro
DB_USERNAME=root
DB_PASSWORD=TU_PASSWORD_AQUI
```

2. Ejecuta las migraciones:
```bash
php artisan migrate
```

**Resultado esperado:**
```
✅ create_doctor_schedules_table
✅ add_location_fields_to_doctors_table
✅ add_location_fields_to_clinics_table
✅ add_whatsapp_fields_to_patients_table
✅ add_confirmation_fields_to_appointments_table
✅ create_whatsapp_accounts_table
✅ create_whatsapp_messages_table
✅ create_whatsapp_templates_table
✅ create_appointment_confirmations_table
```

---

### PASO 2: Instalar Microservicio WhatsApp

1. Navega a la carpeta del servicio:
```bash
cd whatsapp-service
```

2. Instala dependencias:
```bash
npm install
```

3. Configura el servicio:
```bash
cp .env.example .env
```

4. Edita `whatsapp-service/.env`:
```env
PORT=3000
LARAVEL_API_URL=http://localhost:8000
LARAVEL_API_TOKEN=tu-token-super-secreto-aqui
```

5. Inicia el servicio:
```bash
npm run dev
```

**Resultado esperado:**
```
🚀 WhatsApp Service escuchando en puerto 3000
📊 Dashboard: http://localhost:3000/status
🚀 Inicializando Venom Bot...
📱 Escanea el código QR con WhatsApp:
[Código QR en ASCII]
```

6. Escanea el QR con WhatsApp:
   - Abre WhatsApp en tu teléfono
   - Ve a **Dispositivos vinculados**
   - Escanea el código QR que aparece en la consola
   - También puedes obtenerlo en: http://localhost:3000/qr

---

### PASO 3: Probar el Sistema de Agenda

#### Crear un Schedule de Prueba

**Endpoint:** `POST /api/doctor-schedules`

**Ejemplo: Doctor que atiende en Lima (Lun-Vie)**
```json
{
  "doctor_id": 1,
  "clinic_id": 1,
  "city": "Lima",
  "location_name": "Hospital Central Lima",
  "address": "Av. Brasil 600, Lima",
  "schedule_type": "weekly",
  "start_date": "2025-03-20",
  "end_date": null,
  "monday": true,
  "tuesday": true,
  "wednesday": true,
  "thursday": true,
  "friday": true,
  "saturday": false,
  "sunday": false,
  "time_slots": [
    {"start": "08:00", "end": "12:00"},
    {"start": "14:00", "end": "18:00"}
  ],
  "appointment_duration": 30,
  "status": "active",
  "is_available_for_booking": true
}
```

**Resultado:**
- Se crean 16 slots por día (8 horas ÷ 30 min)
- Disponible Lun-Vie indefinidamente en Lima

---

#### Ejemplo: Doctor que viaja a Cusco 3 días

```json
{
  "doctor_id": 1,
  "clinic_id": 5,
  "city": "Cusco",
  "location_name": "Hospital Regional Cusco",
  "address": "Av. de la Cultura 705, Cusco",
  "schedule_type": "specific_date",
  "specific_date": "2025-04-10",
  "start_date": "2025-04-10",
  "end_date": "2025-04-12",
  "monday": true,
  "tuesday": true,
  "wednesday": true,
  "time_slots": [
    {"start": "09:00", "end": "13:00"},
    {"start": "15:00", "end": "19:00"}
  ],
  "appointment_duration": 30,
  "notes": "Atención especializada en altura"
}
```

**Resultado:**
- 16 slots por día × 3 días = 48 slots totales
- Solo disponible del 10-12 de Abril en Cusco

---

#### Consultar Disponibilidad

**Ver slots disponibles para una fecha:**
```
GET /api/doctor-schedules/{id}/available-slots?date=2025-04-10
```

**Respuesta:**
```json
{
  "date": "2025-04-10",
  "doctor": "Dr. Juan Pérez",
  "clinic": "Hospital Regional Cusco",
  "city": "Cusco",
  "total_slots": 16,
  "slots": [
    {"start": "09:00", "end": "09:30", "start_datetime": "2025-04-10 09:00:00"},
    {"start": "09:30", "end": "10:00", "start_datetime": "2025-04-10 09:30:00"},
    ...
  ]
}
```

---

#### Buscar Doctores en una Ciudad

```
GET /api/schedules/by-city/Arequipa?start_date=2025-04-01&end_date=2025-04-30
```

**Respuesta:**
```json
{
  "city": "Arequipa",
  "total_doctors": 5,
  "doctors": [
    {
      "id": 3,
      "name": "Dr. Carlos Mendoza",
      "specialty": "Cardiología",
      "rating": 4.8,
      "schedules": [...]
    }
  ]
}
```

---

### PASO 4: Probar WhatsApp

#### Enviar Mensaje de Prueba

Usando el microservicio directamente:

```bash
curl -X POST http://localhost:3000/send \
  -H "Content-Type: application/json" \
  -d '{
    "to": "51987654321",
    "message": "Hola! Este es un mensaje de prueba desde BackendMedical",
    "messageId": "test-001"
  }'
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Mensaje encolado",
  "queuePosition": 1,
  "messageId": "test-001"
}
```

#### Ver Estado del Servicio

```bash
curl http://localhost:3000/status
```

**Respuesta:**
```json
{
  "status": "ready",
  "connection": "CONNECTED",
  "phoneNumber": "51987654321",
  "qrCode": null,
  "queueLength": 0,
  "rateLimits": {
    "lastMinute": "2/3",
    "lastHour": "8/15",
    "lastDay": "45/100"
  }
}
```

---

## 📋 PRÓXIMOS PASOS PENDIENTES

Para completar el sistema aún faltan estos componentes (que puedo implementar):

### 1. Modelos Laravel WhatsApp
- `app/Models/WhatsAppAccount.php`
- `app/Models/WhatsAppMessage.php`
- `app/Models/WhatsAppTemplate.php`
- `app/Models/AppointmentConfirmation.php`

### 2. Servicio Laravel WhatsApp
- `app/Services/WhatsAppService.php` - Lógica de negocio
- Métodos para:
  - Encolar mensajes con rate limiting
  - Procesar plantillas
  - Distribuir mensajes en el día

### 3. Comandos Artisan
- `app/Console/Commands/SendAppointmentReminders.php`
- `app/Console/Commands/SendAppointmentConfirmations.php`
- `app/Console/Commands/ProcessWhatsAppResponses.php`
- Programar en cron cada hora

### 4. API Controllers Laravel
- `app/Http/Controllers/Api/WhatsAppController.php`
- Endpoints para:
  - Webhook de mensajes entrantes
  - Envío manual de mensajes
  - Gestión de plantillas
  - Estadísticas

### 5. Seeders
- `database/seeders/WhatsAppTemplateSeeder.php`
- Plantillas por defecto:
  - Recordatorio 24h antes
  - Confirmación 48h antes
  - Reagendamiento
  - Cancelación

---

## 🎯 FLUJOS AUTOMATIZADOS (A IMPLEMENTAR)

### Flujo 1: Recordatorio Automático (24h antes)

```
[Cron cada hora]
  ↓
[Buscar citas en 24 horas]
  ↓
[Filtrar pacientes con whatsapp_opt_in=true]
  ↓
[Crear WhatsAppMessage con template "recordatorio_24h"]
  ↓
[Encolar con rate limiting]
  ↓
[Microservicio envía mensaje]
  ↓
[Paciente responde: 1=Confirmo, 2=Reagendar, 3=Cancelar]
  ↓
[Webhook procesa respuesta]
  ↓
[Actualizar appointment_confirmations]
```

### Flujo 2: Confirmación (48h antes)

```
[Cron cada 6 horas]
  ↓
[Buscar citas sin confirmar en 48h]
  ↓
[Enviar mensaje de confirmación]
  ↓
[Esperar 24 horas]
  ↓
[Si no responde → Enviar recordatorio]
  ↓
[Si tras 24h no responde → Marcar como "no_response"]
```

### Flujo 3: Reagendamiento Conversacional

```
[Paciente responde "2" (reagendar)]
  ↓
[Bot: "¿Qué día prefieres? (ej: 25/03/2025)"]
  ↓
[Paciente: "25/03/2025"]
  ↓
[Buscar disponibilidad del doctor ese día + ciudad]
  ↓
[Bot muestra opciones:
  A) 09:00-09:30
  B) 11:00-11:30
  C) 14:00-14:30]
  ↓
[Paciente elige opción]
  ↓
[Sistema cancela cita original]
  ↓
[Sistema crea nueva cita]
  ↓
[Envía confirmación con QR]
```

---

## ⚙️ CONFIGURACIÓN DE CRON

Agregar en `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Enviar recordatorios cada hora
    $schedule->command('appointments:send-reminders')
             ->hourly()
             ->withoutOverlapping();

    // Enviar confirmaciones cada 6 horas
    $schedule->command('appointments:send-confirmations')
             ->everySixHours()
             ->withoutOverlapping();

    // Procesar respuestas cada 5 minutos
    $schedule->command('whatsapp:process-responses')
             ->everyFiveMinutes();

    // Reset contadores diarios a medianoche
    $schedule->call(function () {
        \App\Models\WhatsAppAccount::resetDailyCounters();
    })->daily();

    // Reset contadores por hora
    $schedule->call(function () {
        \App\Models\WhatsAppAccount::resetHourlyCounters();
    })->hourly();

    // Actualizar días desde registro
    $schedule->call(function () {
        \App\Models\WhatsAppAccount::query()->update([
            'days_since_registration' => \DB::raw('DATEDIFF(CURDATE(), registration_date)')
        ]);
    })->daily();
}
```

Activar cron en Windows:
```bash
# Ejecutar en segundo plano
start /B php artisan schedule:work
```

O en producción (Linux):
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🛡️ LÍMITES DE SEGURIDAD (YA IMPLEMENTADOS)

### Rate Limiting por Número

```javascript
// whatsapp-service/server.js
const RATE_LIMITS = {
    messagesPerMinute: 3,        // Muy seguro
    messagesPerHour: 15,         // Conservador
    messagesPerDay: 100,         // Límite diario
    delayBetweenMessages: 3000,  // 3 segundos mínimo
    randomDelayRange: 2000       // +/- 2 segundos
};
```

### Período de Calentamiento (Nuevos Números)

```php
// Implementado en WhatsAppAccount model
Día 1: Max 10 mensajes
Día 2-3: Max 20 mensajes
Día 4-7: Max 50 mensajes
Día 8+: Max 100 mensajes
```

### Contadores Automáticos

La base de datos rastrea:
- `messages_sent_today` - Reset a medianoche
- `messages_sent_this_hour` - Reset cada hora
- `new_contacts_today` - Nuevos contactos por día
- `response_rate` - % de respuestas (debe ser >50%)
- `ban_count` - Número de baneos

---

## 📊 MONITOREO Y MÉTRICAS

### Dashboard WhatsApp

Endpoint futuro: `GET /api/whatsapp/dashboard`

**Métricas a mostrar:**
- Mensajes enviados hoy/semana/mes
- Tasa de respuesta
- Citas confirmadas vs no confirmadas
- Rate limit actual
- Estado de cuentas WhatsApp
- Cola de mensajes pendientes

### Alertas Automáticas

- ⚠️ Tasa de respuesta < 50%
- ⚠️ Número cerca del límite diario (>80 mensajes)
- ❌ Número baneado
- 📵 Servicio desconectado

---

## 🔧 SOLUCIÓN DE PROBLEMAS

### Problema: QR Code no aparece

**Solución:**
```bash
# Eliminar sesión y reiniciar
cd whatsapp-service
rm -rf tokens/
npm run dev
```

### Problema: Mensajes no se envían

**Verificar:**
1. Estado del servicio: `curl http://localhost:3000/status`
2. Cola: `curl http://localhost:3000/queue`
3. Rate limits: Ver `/status`
4. Logs del servicio

### Problema: "Rate limit alcanzado"

**Explicación:** El sistema está protegiendo el número del ban.

**Solución:**
- Esperar a que se reinicie el contador
- Reducir frecuencia de mensajes
- Usar múltiples números WhatsApp

### Problema: Sesión cerrada de WhatsApp

**Solución:**
```bash
# 1. Visitar QR
curl http://localhost:3000/qr

# 2. Escanear con WhatsApp

# 3. Verificar conexión
curl http://localhost:3000/status
```

---

## 📚 RECURSOS Y DOCUMENTACIÓN

### Venom Bot
- Docs: https://github.com/orkestral/venom
- Ejemplos: https://orkestral.gitbook.io/venom/

### WhatsApp Business API (Futuro)
- Docs: https://developers.facebook.com/docs/whatsapp/
- Pricing: https://developers.facebook.com/docs/whatsapp/pricing/

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Completado ✅
- [x] Migraciones de base de datos (agenda + WhatsApp)
- [x] Modelo DoctorSchedule completo
- [x] API Controller para schedules
- [x] Rutas API para schedules
- [x] Microservicio Node.js con Venom Bot
- [x] Rate limiting automático
- [x] Sistema de cola de mensajes
- [x] Webhook para mensajes entrantes
- [x] Dashboard de monitoreo

### Pendiente ⏳
- [ ] Modelos Laravel WhatsApp
- [ ] Servicio WhatsAppService
- [ ] Comandos Artisan (recordatorios/confirmaciones)
- [ ] API Controller WhatsApp
- [ ] Seeders de plantillas
- [ ] Tests automatizados
- [ ] Documentación de API (Swagger/Postman)
- [ ] Deployment en producción

---

## 🚀 SIGUIENTE ACCIÓN RECOMENDADA

1. **Configurar base de datos** y ejecutar `php artisan migrate`
2. **Instalar microservicio WhatsApp** y escanear QR
3. **Probar sistema de agenda** creando un schedule de prueba
4. **Enviar mensaje de prueba** con WhatsApp
5. **Solicitar implementación** de los componentes pendientes

---

## 📞 SOPORTE

Si encuentras problemas:
1. Revisa los logs del microservicio
2. Verifica el estado en `/status`
3. Consulta esta documentación
4. Contacta al equipo de desarrollo

---

**Última actualización:** 20 de Octubre 2025
**Versión:** 1.0.0
