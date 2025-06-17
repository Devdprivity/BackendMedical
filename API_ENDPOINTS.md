# 🏥 API Endpoints - Sistema Clínica Admin

## **Base URL:** `https://backendmedical-main-kqne9d.laravel.cloud/api`

---

## 🔐 **1. AUTENTICACIÓN**

### Login
```http
POST /auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Respuesta:**
```json
{
  "user": {
    "id": 1,
    "name": "Administrador",
    "email": "admin@example.com",
    "role": "admin"
  },
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

### Registro
```http
POST /auth/register
Content-Type: application/json

{
  "name": "Nuevo Usuario",
  "email": "usuario@email.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "admin"
}
```

### Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

### Usuario Actual
```http
GET /auth/user
Authorization: Bearer {token}
```

---

## 📊 **2. DASHBOARD**

### Estadísticas Generales
```http
GET /dashboard/stats
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "patients": {
    "total": 150,
    "active": 120,
    "with_pending_appointments": 25,
    "with_allergies": 30
  },
  "appointments": {
    "today": 15,
    "pending": 45,
    "completed": 200,
    "cancelled": 10
  },
  "surgeries": {
    "scheduled": 5,
    "today": 2,
    "completed": 50,
    "success_rate": 95.5
  }
}
```

### Actividad Reciente
```http
GET /dashboard/recent-activity
Authorization: Bearer {token}
```

---

## 🏢 **3. CLÍNICAS**

### Listar Clínicas
```http
GET /clinics
Authorization: Bearer {token}
```

### Crear Clínica
```http
POST /clinics
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Clínica Central",
  "address": "Av. Principal 123, Ciudad",
  "phone": "555-0123",
  "email": "info@clinicacentral.com",
  "status": "active"
}
```

### Ver Clínica Específica
```http
GET /clinics/{id}
Authorization: Bearer {token}
```

### Actualizar Clínica
```http
PUT /clinics/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Clínica Central Actualizada",
  "address": "Nueva dirección",
  "phone": "555-9999"
}
```

### Eliminar Clínica
```http
DELETE /clinics/{id}
Authorization: Bearer {token}
```

### Doctores de una Clínica
```http
GET /clinics/{id}/doctors
Authorization: Bearer {token}
```

### Pacientes de una Clínica
```http
GET /clinics/{id}/patients
Authorization: Bearer {token}
```

### Citas de una Clínica
```http
GET /clinics/{id}/appointments
Authorization: Bearer {token}
```

---

## 👨‍⚕️ **4. DOCTORES**

### Listar Doctores
```http
GET /doctors
Authorization: Bearer {token}
```

### Crear Doctor
```http
POST /doctors
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 2,
  "specialty": "Cardiología",
  "license_number": "MD12345",
  "clinic_id": 1,
  "phone": "555-0456",
  "schedule": {
    "monday": "08:00-17:00",
    "tuesday": "08:00-17:00",
    "wednesday": "08:00-17:00",
    "thursday": "08:00-17:00",
    "friday": "08:00-17:00"
  }
}
```

### Ver Doctor Específico
```http
GET /doctors/{id}
Authorization: Bearer {token}
```

### Actualizar Doctor
```http
PUT /doctors/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "specialty": "Cardiología Pediátrica",
  "phone": "555-0789"
}
```

### Eliminar Doctor
```http
DELETE /doctors/{id}
Authorization: Bearer {token}
```

### Citas de un Doctor
```http
GET /doctors/{id}/appointments
Authorization: Bearer {token}
```

### Citas de Hoy de un Doctor
```http
GET /doctors/{id}/today-appointments
Authorization: Bearer {token}
```

### Cirugías de un Doctor
```http
GET /doctors/{id}/surgeries
Authorization: Bearer {token}
```

### Exámenes Solicitados por un Doctor
```http
GET /doctors/{id}/exams
Authorization: Bearer {token}
```

---

## 👥 **5. PACIENTES**

### Listar Pacientes
```http
GET /patients
Authorization: Bearer {token}
```

### Crear Paciente
```http
POST /patients
Authorization: Bearer {token}
Content-Type: application/json

{
  "first_name": "Juan",
  "last_name": "Pérez",
  "email": "juan.perez@email.com",
  "phone": "555-0123",
  "date_of_birth": "1990-01-15",
  "gender": "male",
  "address": "Calle 456, Ciudad",
  "emergency_contact_name": "María Pérez",
  "emergency_contact_phone": "555-0124",
  "blood_type": "O+",
  "identification_number": "12345678"
}
```

### Ver Paciente Específico
```http
GET /patients/{id}
Authorization: Bearer {token}
```

### Actualizar Paciente
```http
PUT /patients/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "first_name": "Juan Carlos",
  "phone": "555-9999",
  "address": "Nueva dirección 789"
}
```

### Eliminar Paciente
```http
DELETE /patients/{id}
Authorization: Bearer {token}
```

### Historial Médico del Paciente
```http
GET /patients/{id}/medical-history
Authorization: Bearer {token}
```

### Actualizar Historial Médico
```http
PUT /patients/{id}/medical-history
Authorization: Bearer {token}
Content-Type: application/json

{
  "allergies": "Penicilina, Aspirina",
  "chronic_conditions": "Diabetes tipo 2, Hipertensión",
  "medications": "Metformina 500mg, Losartán 50mg",
  "family_history": "Diabetes (padre), Hipertensión (madre)",
  "surgical_history": "Apendicectomía (2015)",
  "notes": "Paciente colaborador, seguimiento regular"
}
```

### Signos Vitales del Paciente
```http
GET /patients/{id}/vital-signs
Authorization: Bearer {token}
```

### Agregar Signos Vitales
```http
POST /patients/{id}/vital-signs
Authorization: Bearer {token}
Content-Type: application/json

{
  "blood_pressure": "120/80",
  "heart_rate": 75,
  "temperature": 36.5,
  "weight": 70.5,
  "height": 175,
  "respiratory_rate": 18,
  "oxygen_saturation": 98,
  "recorded_at": "2024-01-15 10:30:00"
}
```

### Citas del Paciente
```http
GET /patients/{id}/appointments
Authorization: Bearer {token}
```

### Cirugías del Paciente
```http
GET /patients/{id}/surgeries
Authorization: Bearer {token}
```

### Exámenes Médicos del Paciente
```http
GET /patients/{id}/exams
Authorization: Bearer {token}
```

### Facturas del Paciente
```http
GET /patients/{id}/invoices
Authorization: Bearer {token}
```

---

## 📅 **6. CITAS**

### Listar Citas
```http
GET /appointments
Authorization: Bearer {token}
```

### Crear Cita
```http
POST /appointments
Authorization: Bearer {token}
Content-Type: application/json

{
  "patient_id": 1,
  "doctor_id": 1,
  "appointment_date": "2024-01-15",
  "appointment_time": "10:00:00",
  "reason": "Consulta general",
  "status": "scheduled",
  "notes": "Primera consulta del paciente"
}
```

### Ver Cita Específica
```http
GET /appointments/{id}
Authorization: Bearer {token}
```

### Actualizar Cita
```http
PUT /appointments/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "appointment_date": "2024-01-16",
  "appointment_time": "11:00:00",
  "reason": "Consulta de seguimiento"
}
```

### Eliminar Cita
```http
DELETE /appointments/{id}
Authorization: Bearer {token}
```

### Citas de Hoy
```http
GET /appointments/today
Authorization: Bearer {token}
```

### Cambiar Estado de Cita
```http
PATCH /appointments/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "completed"
}
```

**Estados válidos:** `scheduled`, `in_progress`, `completed`, `cancelled`, `no_show`

---

## 🏥 **7. CIRUGÍAS**

### Listar Cirugías
```http
GET /surgeries
Authorization: Bearer {token}
```

### Crear Cirugía
```http
POST /surgeries
Authorization: Bearer {token}
Content-Type: application/json

{
  "patient_id": 1,
  "doctor_id": 1,
  "surgery_type": "Apendicectomía",
  "scheduled_date": "2024-01-20",
  "scheduled_time": "08:00:00",
  "duration_minutes": 60,
  "status": "scheduled",
  "operating_room": "Quirófano 1",
  "notes": "Cirugía electiva"
}
```

### Ver Cirugía Específica
```http
GET /surgeries/{id}
Authorization: Bearer {token}
```

### Actualizar Cirugía
```http
PUT /surgeries/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "scheduled_date": "2024-01-21",
  "operating_room": "Quirófano 2"
}
```

### Eliminar Cirugía
```http
DELETE /surgeries/{id}
Authorization: Bearer {token}
```

### Cirugías de Hoy
```http
GET /surgeries/today
Authorization: Bearer {token}
```

### Cambiar Estado de Cirugía
```http
PATCH /surgeries/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "completed",
  "actual_start_time": "08:15:00",
  "actual_end_time": "09:30:00",
  "complications": "Ninguna",
  "outcome": "Exitosa"
}
```

**Estados válidos:** `scheduled`, `in_progress`, `completed`, `cancelled`, `postponed`

---

## 🧪 **8. EXÁMENES MÉDICOS**

### Listar Exámenes
```http
GET /medical-exams
Authorization: Bearer {token}
```

### Crear Examen
```http
POST /medical-exams
Authorization: Bearer {token}
Content-Type: application/json

{
  "patient_id": 1,
  "doctor_id": 1,
  "exam_type": "Hemograma completo",
  "ordered_date": "2024-01-15",
  "scheduled_date": "2024-01-16",
  "status": "ordered",
  "instructions": "Paciente debe venir en ayunas",
  "urgency": "routine"
}
```

### Ver Examen Específico
```http
GET /medical-exams/{id}
Authorization: Bearer {token}
```

### Actualizar Examen
```http
PUT /medical-exams/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "scheduled_date": "2024-01-17",
  "urgency": "urgent"
}
```

### Eliminar Examen
```http
DELETE /medical-exams/{id}
Authorization: Bearer {token}
```

### Obtener Resultado de Examen
```http
GET /medical-exams/{id}/result
Authorization: Bearer {token}
```

### Agregar Resultado de Examen
```http
POST /medical-exams/{id}/result
Authorization: Bearer {token}
Content-Type: application/json

{
  "results": "Hemoglobina: 14.2 g/dL, Leucocitos: 7500/μL",
  "interpretation": "Valores dentro de parámetros normales",
  "performed_date": "2024-01-16",
  "technician_name": "Lab. García"
}
```

### Actualizar Resultado de Examen
```http
PUT /medical-exams/{id}/result
Authorization: Bearer {token}
Content-Type: application/json

{
  "results": "Resultados actualizados",
  "interpretation": "Nueva interpretación"
}
```

---

## 💰 **9. FACTURAS**

### Listar Facturas
```http
GET /invoices
Authorization: Bearer {token}
```

### Crear Factura
```http
POST /invoices
Authorization: Bearer {token}
Content-Type: application/json

{
  "patient_id": 1,
  "appointment_id": 1,
  "invoice_number": "INV-2024-001",
  "issue_date": "2024-01-15",
  "due_date": "2024-02-15",
  "subtotal": 100.00,
  "tax": 15.00,
  "total": 115.00,
  "payment_status": "pending",
  "items": [
    {
      "description": "Consulta médica",
      "quantity": 1,
      "unit_price": 100.00,
      "total": 100.00
    }
  ]
}
```

### Ver Factura Específica
```http
GET /invoices/{id}
Authorization: Bearer {token}
```

### Actualizar Factura
```http
PUT /invoices/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "due_date": "2024-02-20",
  "total": 120.00
}
```

### Eliminar Factura
```http
DELETE /invoices/{id}
Authorization: Bearer {token}
```

### Cambiar Estado de Pago
```http
PATCH /invoices/{id}/payment-status
Authorization: Bearer {token}
Content-Type: application/json

{
  "payment_status": "paid",
  "payment_date": "2024-01-15",
  "payment_method": "credit_card"
}
```

**Estados válidos:** `pending`, `paid`, `overdue`, `cancelled`

### Facturas Vencidas
```http
GET /invoices/overdue
Authorization: Bearer {token}
```

---

## 💊 **10. MEDICAMENTOS**

### Listar Medicamentos
```http
GET /medications
Authorization: Bearer {token}
```

### Crear Medicamento
```http
POST /medications
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Paracetamol",
  "generic_name": "Acetaminofén",
  "brand": "Tylenol",
  "dosage": "500mg",
  "form": "Tableta",
  "manufacturer": "Johnson & Johnson",
  "stock_quantity": 100,
  "min_stock_level": 20,
  "unit_price": 0.50,
  "expiration_date": "2025-12-31",
  "lot_number": "LOT123",
  "status": "active"
}
```

### Ver Medicamento Específico
```http
GET /medications/{id}
Authorization: Bearer {token}
```

### Actualizar Medicamento
```http
PUT /medications/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "stock_quantity": 150,
  "unit_price": 0.55
}
```

### Eliminar Medicamento
```http
DELETE /medications/{id}
Authorization: Bearer {token}
```

### Medicamentos con Stock Bajo
```http
GET /medications/low-stock
Authorization: Bearer {token}
```

### Medicamentos por Vencer
```http
GET /medications/expiring
Authorization: Bearer {token}
```

### Agregar Movimiento de Inventario
```http
POST /medications/{id}/movement
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "in",
  "quantity": 50,
  "reason": "Compra",
  "reference": "PO-2024-001",
  "unit_cost": 0.45
}
```

**Tipos de movimiento:** `in` (entrada), `out` (salida)

### Historial de Movimientos
```http
GET /medications/{id}/movements
Authorization: Bearer {token}
```

---

## 🔧 **CÓDIGOS DE RESPUESTA HTTP**

- **200** - OK (Operación exitosa)
- **201** - Created (Recurso creado exitosamente)
- **400** - Bad Request (Datos inválidos)
- **401** - Unauthorized (Token inválido o faltante)
- **403** - Forbidden (Sin permisos)
- **404** - Not Found (Recurso no encontrado)
- **422** - Unprocessable Entity (Errores de validación)
- **500** - Internal Server Error (Error del servidor)

---

## 📝 **NOTAS IMPORTANTES**

### Autenticación
- Todos los endpoints (excepto login y register) requieren el header: `Authorization: Bearer {token}`
- Los tokens se obtienen en el login y deben incluirse en cada request

### Paginación
- Los endpoints de listado soportan paginación con parámetros `page` y `per_page`
- Ejemplo: `GET /patients?page=2&per_page=20`

### Filtros
- Muchos endpoints soportan filtros por query parameters
- Ejemplo: `GET /appointments?status=scheduled&date=2024-01-15`

### Roles de Usuario
- **admin**: Acceso completo a todas las funciones
- **doctor**: Acceso a pacientes, citas, cirugías y exámenes
- **staff**: Acceso limitado según configuración

### Formatos de Fecha
- Fechas: `YYYY-MM-DD` (ejemplo: `2024-01-15`)
- Fecha y hora: `YYYY-MM-DD HH:MM:SS` (ejemplo: `2024-01-15 10:30:00`)
- Horas: `HH:MM:SS` (ejemplo: `10:30:00`)

---

## 🚀 **EJEMPLOS DE USO CON CURL**

### Login y obtener token
```bash
curl -X POST "https://backendmedical-main-kqne9d.laravel.cloud/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### Crear un paciente
```bash
curl -X POST "https://backendmedical-main-kqne9d.laravel.cloud/api/patients" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Juan",
    "last_name": "Pérez",
    "email": "juan@email.com",
    "phone": "555-0123",
    "date_of_birth": "1990-01-15",
    "gender": "male"
  }'
```

### Obtener estadísticas del dashboard
```bash
curl -X GET "https://backendmedical-main-kqne9d.laravel.cloud/api/dashboard/stats" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

**🏥 Sistema Clínica Admin - API Documentation**  
**Version:** 1.0  
**Base URL:** https://backendmedical-main-kqne9d.laravel.cloud/api 