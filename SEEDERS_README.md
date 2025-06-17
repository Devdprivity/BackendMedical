# 🏥 Sistema de Seeders - Clínica Admin

Este documento explica el sistema completo de seeders para poblar la base de datos con datos realistas de prueba.

## 📋 **Contenido de los Seeders**

### **1. AdminUserSeeder**
- ✅ Crea usuario administrador principal
- 📧 Email: `admin@example.com` / Password: `password`
- 🔑 Rol: `admin` con acceso completo

### **2. ClinicSeeder** 
- ✅ 5 clínicas especializadas:
  - Clínica Central (Medicina General, Cardiología, Pediatría)
  - Hospital San Rafael (Cirugía, Traumatología, UCI)  
  - Centro Médico Esperanza (Dermatología, Oftalmología)
  - Clínica Pediátrica Los Ángeles (Pediatría especializada)
  - Instituto Cardiovascular (Cardiología avanzada)

### **3. DoctorUserSeeder**
- ✅ 10 doctores especializados con usuarios asociados
- 🩺 Especialidades: Cardiología, Pediatría, Cirugía, Ginecología, Neurología, Dermatología, Traumatología, Oftalmología, Medicina Interna, Psiquiatría
- 📧 Email: `[nombre].[apellido]@clinica.com` / Password: `doctor123`
- 📋 Incluye horarios, tarifas, educación y biografías

### **4. PatientSeeder**
- ✅ 10 pacientes diversos con datos realistas
- 👨‍👩‍👧‍👦 Incluye adultos, niños, diferentes condiciones médicas
- 🩺 Con historial médico completo (alergias, condiciones crónicas, medicamentos)
- 🆘 Contactos de emergencia para cada paciente
- 🩸 Tipos de sangre, seguros médicos, ocupaciones

### **5. MedicationSeeder**
- ✅ 20 medicamentos comunes del inventario
- 💊 Categorías: Analgésicos, Antibióticos, Cardiovasculares, Antidiabéticos, Respiratorios, Vitaminas, Hormonales
- 📦 Con stock, precios, fechas de vencimiento
- ⚠️ Algunos con stock bajo para testing
- 📋 Incluye prescripción requerida, fabricantes, lotes

### **6. AppointmentSeeder**
- ✅ Citas para las próximas 4 semanas
- 📅 Incluye citas para hoy con diferentes estados
- ⏰ Horarios realistas de 8:00 AM a 5:00 PM
- 📊 Estados: scheduled, in_progress, completed, cancelled, no_show
- 📝 Razones variadas: consultas, controles, seguimientos

### **7. MedicalExamSeeder + ExamResult**
- ✅ 50 exámenes médicos diversos
- 🧪 Tipos: Hemogramas, radiografías, ECG, ecografías, tomografías, etc.
- 📊 Estados: ordered, scheduled, in_progress, completed, cancelled
- 🔬 Resultados realistas para exámenes completados
- 👨‍🔬 Con técnicos responsables e interpretaciones

### **8. SurgerySeeder**
- ✅ 30+ cirugías programadas
- 🏥 Tipos: Apendicectomía, colecistectomía, artroscopia, cesárea, etc.
- 🏢 Quirófanos asignados
- 📊 Estados: scheduled, in_progress, completed, cancelled, postponed
- ⚕️ Con anestesia, complicaciones, resultados

### **9. VitalSignSeeder**
- ✅ Múltiples registros por paciente (3-8 registros)
- 💓 Signos vitales realistas basados en edad y condiciones
- 📏 Presión arterial, frecuencia cardíaca, temperatura, peso, talla, BMI
- 🩺 Registros recientes para testing
- 👩‍⚕️ Con enfermeras responsables

### **10. InvoiceSeeder**
- ✅ 100+ facturas de los últimos 3 meses
- 💰 Servicios variados: consultas, exámenes, cirugías
- 📊 Estados: pending, paid, overdue (70% pagadas, 20% pendientes, 10% vencidas)
- 🧾 Con IVA, descuentos, métodos de pago
- 💳 Facturas específicas para testing

---

## 🚀 **Ejecutar Seeders**

### **Ejecutar todos los seeders:**
```bash
php artisan db:seed
```

### **Ejecutar seeder específico:**
```bash
php artisan db:seed --class=PatientSeeder
php artisan db:seed --class=MedicationSeeder
```

### **Refrescar base de datos y seeders:**
```bash
php artisan migrate:fresh --seed
```

---

## 🎯 **Datos de Prueba Disponibles**

### **📊 Estadísticas del Sistema:**
- 🏢 **5 Clínicas** con especialidades
- 👨‍⚕️ **10 Doctores** especializados  
- 👥 **10 Pacientes** con historiales completos
- 💊 **20 Medicamentos** en inventario
- 📅 **Citas** para 4 semanas (incluyendo hoy)
- 🧪 **50 Exámenes** médicos con resultados
- 🏥 **30+ Cirugías** programadas
- 💓 **Signos vitales** históricos por paciente
- 💰 **100+ Facturas** con diferentes estados

### **🔑 Usuarios para Testing:**

| Tipo | Email | Password | Rol |
|------|-------|----------|-----|
| Admin | `admin@example.com` | `password` | admin |
| Doctor | `carlos.rodriguez@clinica.com` | `doctor123` | doctor |
| Doctor | `maria.gonzalez@clinica.com` | `doctor123` | doctor |
| Doctor | `luis.martinez@clinica.com` | `doctor123` | doctor |

---

## 🧪 **Casos de Prueba Específicos**

### **📅 Citas de Hoy:**
- ✅ Cita completada (Control cardiológico)
- 🔄 Cita en progreso (Control pediátrico) 
- ⏰ Citas programadas (Pre-quirúrgica, Control prenatal)

### **🏥 Cirugías de Hoy:**
- ✅ Colecistectomía completada (08:00 AM)
- 🔄 Artroscopia en progreso (02:00 PM)
- ⏰ Implante marcapasos programado (mañana)

### **💊 Medicamentos con Alertas:**
- ⚠️ **Tramadol**: Stock bajo (15 unidades, mínimo 20)
- ⚠️ **Insulina NPH**: Stock bajo + próximo a vencer

### **💰 Facturas para Testing:**
- 💳 Factura pendiente de hoy (Consulta cardiológica)
- ✅ Factura pagada ayer (Cirugía con seguro)
- ❌ Factura vencida (45 días atrás)

### **🧪 Exámenes con Resultados:**
- ✅ Hemogramas con valores normales/alterados
- ✅ Radiografías con hallazgos
- ✅ ECG con interpretaciones
- ✅ Análisis de orina con diferentes resultados

---

## 📡 **Testing con API**

### **Login Administrativo:**
```bash
curl -X POST "https://backendmedical-main-kqne9d.laravel.cloud/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### **Login Doctor:**
```bash
curl -X POST "https://backendmedical-main-kqne9d.laravel.cloud/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"carlos.rodriguez@clinica.com","password":"doctor123"}'
```

### **Estadísticas Dashboard:**
```bash
curl -X GET "https://backendmedical-main-kqne9d.laravel.cloud/api/dashboard/stats" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### **Citas de Hoy:**
```bash
curl -X GET "https://backendmedical-main-kqne9d.laravel.cloud/api/appointments/today" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🔧 **Orden de Dependencias**

Los seeders se ejecutan en este orden para respetar las dependencias:

1. **AdminUserSeeder** → Usuarios base
2. **ClinicSeeder** → Clínicas disponibles  
3. **DoctorUserSeeder** → Doctores (depende de clínicas)
4. **PatientSeeder** → Pacientes con historiales
5. **MedicationSeeder** → Inventario de medicamentos
6. **AppointmentSeeder** → Citas (depende de doctores/pacientes)
7. **MedicalExamSeeder** → Exámenes (depende de doctores/pacientes)
8. **SurgerySeeder** → Cirugías (depende de doctores/pacientes)
9. **VitalSignSeeder** → Signos vitales (depende de pacientes)
10. **InvoiceSeeder** → Facturas (depende de citas/pacientes)

---

## ⚡ **Características Especiales**

### **🎲 Datos Aleatorios pero Realistas:**
- Fechas coherentes (pasado/presente/futuro)
- Valores médicos dentro de rangos normales
- Relaciones lógicas entre entidades
- Estados progresivos (programado → en progreso → completado)

### **📊 Datos para Métricas:**
- Distribución realista de estados
- Variedad en tipos de servicios
- Diferentes rangos de fechas
- Casos edge para testing

### **🔍 Búsqueda y Filtros:**
- Datos con diferentes criterios de búsqueda
- Rangos de fechas diversos
- Estados múltiples para filtros
- Categorías variadas

---

## 🌐 **Documentación Completa**

📖 **Ver `API_ENDPOINTS.md`** para documentación completa de endpoints y ejemplos de uso.

🎯 **Sistema listo para testing completo de todas las funcionalidades médicas!** 