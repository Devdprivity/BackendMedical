# Sistema de Administración Médica - Requisitos y Estructura

## Índice de Secciones Identificadas
1. Gestión de Clínicas
2. Gestión de Doctores
3. Gestión de Pacientes
4. Gestión de Citas
5. Gestión de Cirugías
6. Gestión de Exámenes Médicos (Por implementar)
7. Gestión de Facturas (Por implementar)
8. Gestión de Resultados de Exámenes (Por implementar)

---

## 1. Gestión de Clínicas

### Vista Principal
- Contador de clínicas totales
- Contador de clínicas activas
- Total de médicos en todas las clínicas
- Total de pacientes en todas las clínicas

### Campos para CRUD
```
CREATE/UPDATE
- Nombre de la clínica
- Dirección
- Teléfono
- Email
- Director médico
- Año de fundación
- Especialidades disponibles (múltiple selección)
- Horario de atención
- Servicios de emergencia (sí/no)
- Estado (Activa/Inactiva/Mantenimiento)
- Descripción

READ (Lista)
- Nombre
- Estado (con indicador de color)
- Total de doctores
- Total de pacientes
- Total de citas mensuales
- Ingresos mensuales

READ (Detalles)
- Toda la información del CREATE
+ Estadísticas detalladas
  - Total de citas
  - Ingresos mensuales
  - Tasa de ocupación
  - Satisfacción de pacientes
```

## 2. Gestión de Doctores

### Vista Principal
- Total de doctores
- Doctores activos
- Total de pacientes atendidos
- Citas programadas hoy

### Campos para CRUD
```
CREATE/UPDATE
- Nombre completo
- Especialidad (selector)
- Clínica asignada (selector)
- Número de licencia (CMP)
- Email profesional
- Teléfono
- Teléfono de emergencia
- Dirección
- Años de experiencia
- Educación (múltiples entradas)
- Certificaciones (múltiples entradas)
- Idiomas (múltiple selección)
- Estado (Activo/Inactivo/Vacaciones/Licencia)
- Biografía profesional
- Horario de atención por día
  - Lunes a Sábado
  - Múltiples rangos por día

READ (Lista)
- Foto
- Nombre
- Especialidad
- Clínica
- Estado (con indicador de color)
- Total de citas del día
- Calificación promedio

READ (Detalles)
- Toda la información del CREATE
+ Estadísticas
  - Total de pacientes atendidos
  - Citas programadas
  - Años de experiencia
  - Calificación promedio
```

## 3. Gestión de Pacientes

### Vista Principal
- Total de pacientes
- Pacientes activos
- Pacientes con citas pendientes
- Pacientes con alergias

### Campos para CRUD
```
CREATE/UPDATE
Información Personal:
- Nombre completo
- DNI
- Fecha de nacimiento
- Edad (calculada)
- Género
- Tipo de sangre
- Dirección
- Teléfono
- Email
- Estado (Activo/Inactivo)
- Clínica preferida

Contacto de Emergencia:
- Nombre
- Teléfono
- Relación

Información Médica:
- Alergias (múltiples entradas)
- Condiciones preexistentes
- Medicamentos actuales
- Cirugías previas

READ (Lista)
- Nombre
- DNI
- Edad
- Última visita
- Estado
- Clínica
- Próxima cita

READ (Detalles)
- Toda la información del CREATE
+ Historial Médico
  - Consultas previas
  - Medicamentos
  - Cirugías
  - Exámenes
+ Signos Vitales
  - Peso
  - Altura
  - Presión arterial
  - Ritmo cardíaco
  - Temperatura
```

## 4. Gestión de Citas

### Vista Principal
- Total de citas del día
- Citas pendientes
- Citas completadas
- Citas canceladas

### Campos para CRUD
```
CREATE/UPDATE
- Paciente (selector)
- Doctor (selector, filtrado por especialidad)
- Fecha
- Hora
- Tipo de cita
- Motivo de la consulta
- Notas adicionales
- Estado (Programada/Completada/Cancelada/Pendiente)

READ (Lista)
- Fecha y hora
- Paciente
- Doctor
- Especialidad
- Clínica
- Estado (con indicador de color)
- Motivo

READ (Detalles)
- Toda la información del CREATE
+ Información del paciente
+ Información del doctor
+ Historial de cambios de estado
```

## 5. Gestión de Cirugías

### Vista Principal
- Cirugías programadas
- Cirugías del día
- Cirugías completadas
- Tasa de éxito

### Campos para CRUD
```
CREATE/UPDATE
- Paciente (selector)
- Cirujano principal (selector)
- Cirujanos asistentes (múltiple)
- Fecha y hora
- Duración estimada
- Tipo de cirugía
- Sala de operaciones
- Tipo de anestesia
- Equipo necesario
- Notas pre-operatorias
- Estado

READ (Lista)
- Fecha y hora
- Paciente
- Cirujano principal
- Sala
- Estado
- Tipo de cirugía

READ (Detalles)
- Toda la información del CREATE
+ Equipo médico completo
+ Preparaciones necesarias
+ Riesgos identificados
```

## 6. Gestión de Exámenes Médicos

### Vista Principal
- Exámenes pendientes
- Exámenes completados
- Exámenes por tipo
- Resultados pendientes

### Campos para CRUD
```
CREATE/UPDATE
- Paciente (selector)
- Doctor solicitante
- Tipo de examen
- Fecha programada
- Laboratorio/Área
- Preparación requerida
- Notas especiales
- Estado

READ (Lista)
- Fecha
- Paciente
- Tipo de examen
- Doctor solicitante
- Estado
- Resultados disponibles

READ (Detalles)
- Toda la información del CREATE
+ Resultados
+ Valores de referencia
+ Interpretación médica
```

## 7. Gestión de Facturas

### Vista Principal
- Facturas pendientes
- Facturas pagadas
- Total facturado
- Pagos del día

### Campos para CRUD
```
CREATE/UPDATE
- Número de factura
- Paciente
- Fecha de emisión
- Fecha de vencimiento
- Items facturados
  - Consultas
  - Procedimientos
  - Medicamentos
  - Exámenes
- Subtotal
- IGV
- Total
- Estado de pago
- Método de pago
- Notas

READ (Lista)
- Número de factura
- Fecha
- Paciente
- Total
- Estado de pago

READ (Detalles)
- Toda la información del CREATE
+ Historial de pagos
+ Documentos relacionados
```

## 8. Gestión de Resultados de Exámenes

### Vista Principal
- Resultados pendientes
- Resultados entregados
- Resultados por tipo
- Alertas de valores críticos

### Campos para CRUD
```
CREATE/UPDATE
- Examen (referencia)
- Paciente
- Doctor solicitante
- Fecha de resultado
- Tipo de examen
- Valores medidos
- Valores de referencia
- Interpretación
- Estado
- Archivos adjuntos
- Notas adicionales

READ (Lista)
- Fecha
- Paciente
- Tipo de examen
- Doctor
- Estado
- Indicador de valores críticos

READ (Detalles)
- Toda la información del CREATE
+ Historial de resultados anteriores
+ Comparativas
+ Gráficos de evolución
```

## Vista de Bienvenida (Dashboard)

### Componentes Principales
- Resumen general del sistema
- Estadísticas clave
- Actividad reciente
- Alertas y notificaciones

### Campos y Elementos
```
Estadísticas Generales:
- Total de pacientes activos
- Citas programadas hoy
- Cirugías programadas
- Exámenes pendientes
- Facturas por cobrar

Gráficos y Métricas:
- Distribución de pacientes por especialidad
- Ocupación de clínicas
- Ingresos mensuales
- Satisfacción de pacientes

Actividad Reciente:
- Últimas citas registradas
- Últimos pacientes ingresados
- Últimos pagos recibidos
- Últimos exámenes completados

Alertas:
- Medicamentos por vencer
- Citas próximas
- Pagos vencidos
- Resultados críticos de exámenes
```

## Vista de Medicamentos

### Vista Principal
- Total de medicamentos
- Medicamentos con stock bajo
- Medicamentos por vencer
- Valor total del inventario

### Campos para CRUD
```
CREATE/UPDATE
Información Básica:
- Nombre comercial
- Nombre genérico
- Laboratorio fabricante
- Código de barras/SKU
- Categoría terapéutica
- Presentación
- Concentración
- Vía de administración
- Requiere receta (sí/no)
- Controlado (sí/no)

Inventario:
- Stock actual
- Stock mínimo
- Stock máximo
- Ubicación en almacén
- Fecha de vencimiento
- Número de lote
- Costo unitario
- Precio de venta
- Estado (Activo/Descontinuado/Agotado)

Información Adicional:
- Indicaciones principales
- Contraindicaciones
- Efectos secundarios
- Interacciones medicamentosas
- Notas especiales de almacenamiento

READ (Lista)
- Nombre comercial
- Nombre genérico
- Stock actual
- Estado de inventario (con indicador de color)
- Fecha de vencimiento
- Requiere receta
- Precio

READ (Detalles)
- Toda la información del CREATE
+ Historial de movimientos
+ Estadísticas de consumo
+ Proveedores asociados
```

## Vista de Configuración del Sistema

### Vista Principal
- Estado general del sistema
- Últimas actualizaciones
- Alertas de sistema
- Estado de backups

### Campos para Configuración
```
Configuración General:
- Nombre de la institución
- Logo
- Zona horaria
- Idioma predeterminado
- Moneda predeterminada
- Formato de fechas
- Formato de números

Configuración de Seguridad:
- Política de contraseñas
- Tiempo de sesión
- Intentos máximos de login
- Autenticación de dos factores
- IPs permitidas
- Registro de auditoría

Configuración de Notificaciones:
- Correo electrónico
- SMS
- Notificaciones push
- Plantillas de mensajes
- Programación de recordatorios

Configuración de Respaldos:
- Frecuencia de respaldos
- Ubicación de respaldos
- Retención de respaldos
- Encriptación
- Restauración automática

Configuración de Facturación:
- Serie de facturas
- IGV/IVA
- Métodos de pago aceptados
- Términos de pago predeterminados
- Plantillas de documentos

Integraciones:
- APIs externas
- Servicios de laboratorio
- Servicios de imágenes
- Pasarelas de pago
- Servicios de mensajería
```

## Vista de Reportes y Estadísticas

### Tipos de Reportes
```
Reportes Clínicos:
- Pacientes por diagnóstico
- Procedimientos realizados
- Medicamentos más recetados
- Exámenes realizados
- Cirugías por especialidad
- Tiempo promedio de consulta
- Tasa de ocupación por especialidad

Reportes Financieros:
- Ingresos por período
- Ingresos por servicio
- Cuentas por cobrar
- Estado de pagos
- Rentabilidad por servicio
- Costos operativos
- Proyecciones financieras

Reportes de Gestión:
- Productividad médica
- Satisfacción del paciente
- Tiempo de espera
- Cancelaciones y ausencias
- Uso de recursos
- Indicadores de calidad
- Cumplimiento de objetivos

Reportes de Inventario:
- Stock por medicamento
- Movimientos de inventario
- Medicamentos por vencer
- Consumo por servicio
- Pérdidas y ajustes
- Valorización de inventario
- Rotación de productos

Filtros Disponibles:
- Rango de fechas
- Clínica/Sede
- Especialidad
- Doctor
- Tipo de servicio
- Estado
- Categoría

Formatos de Exportación:
- PDF
- Excel
- CSV
- JSON
```

## Consideraciones Adicionales

### Permisos y Roles
- Admin: Acceso total
- Doctor: Acceso a sus pacientes y citas
- Enfermera: Acceso limitado a pacientes
- Recepcionista: Gestión de citas y pacientes
- Contador: Acceso a facturas
- Laboratorista: Gestión de exámenes y resultados

### Integraciones Necesarias
1. Sistema de pagos
2. Laboratorio
3. Farmacia
4. Seguros médicos
5. Notificaciones (SMS/Email)
6. Reportes y estadísticas 

## Estructura de Base de Datos por Vista

### 1. Dashboard (Vista de Bienvenida)
```
Campos Necesarios:
- Pacientes:
  * id
  * status (activo/inactivo)
  * created_at
  * clinic_id

- Citas:
  * id
  * date_time
  * status
  * patient_id
  * doctor_id
  * clinic_id

- Cirugías:
  * id
  * date_time
  * status
  * patient_id
  * main_surgeon_id
  * clinic_id

- Exámenes:
  * id
  * scheduled_date
  * status
  * patient_id
  * requesting_doctor_id

- Facturas:
  * id
  * issue_date
  * due_date
  * total
  * payment_status
  * patient_id

Relaciones:
- Pacientes -> Clínicas
- Citas -> Pacientes, Doctores, Clínicas
- Cirugías -> Pacientes, Doctores, Clínicas
- Exámenes -> Pacientes, Doctores
- Facturas -> Pacientes
```

### 2. Clínicas
```
Campos Necesarios:
- Clínicas:
  * id
  * name
  * address
  * phone
  * email
  * medical_director
  * foundation_year
  * specialties (array)
  * schedule
  * emergency_services
  * status
  * description
  * created_at
  * updated_at

- Doctores por Clínica:
  * clinic_id
  * doctor_id
  * status
  * schedule

- Pacientes por Clínica:
  * clinic_id
  * patient_id
  * preferred_clinic (boolean)

- Estadísticas de Clínica:
  * clinic_id
  * total_appointments
  * monthly_income
  * occupancy_rate
  * patient_satisfaction
  * period (mes/año)

Relaciones:
- Clínicas -> Doctores (muchos a muchos)
- Clínicas -> Pacientes (muchos a muchos)
- Clínicas -> Estadísticas (uno a muchos)
```

### 3. Médicos
```
Campos Necesarios:
- Doctores:
  * id
  * user_id
  * name
  * specialty
  * license_number
  * email
  * phone
  * emergency_phone
  * address
  * experience_years
  * education (array)
  * certifications (array)
  * languages (array)
  * status
  * bio
  * photo_url
  * rating
  * created_at
  * updated_at

- Horarios:
  * doctor_id
  * day_of_week
  * start_time
  * end_time
  * clinic_id

- Estadísticas del Doctor:
  * doctor_id
  * total_patients
  * appointments_today
  * total_appointments
  * average_rating
  * period (mes/año)

Relaciones:
- Doctores -> Usuarios (uno a uno)
- Doctores -> Clínicas (muchos a muchos)
- Doctores -> Horarios (uno a muchos)
- Doctores -> Estadísticas (uno a muchos)
```

### 4. Pacientes
```
Campos Necesarios:
- Pacientes:
  * id
  * name
  * dni
  * birth_date
  * gender
  * blood_type
  * address
  * phone
  * email
  * status
  * preferred_clinic_id
  * created_at
  * updated_at

- Contacto de Emergencia:
  * patient_id
  * name
  * phone
  * relationship

- Historial Médico:
  * patient_id
  * allergies (array)
  * conditions (array)
  * medications (array)
  * surgeries (array)

- Signos Vitales:
  * patient_id
  * weight
  * height
  * blood_pressure
  * heart_rate
  * temperature
  * measured_at

Relaciones:
- Pacientes -> Clínicas (muchos a uno)
- Pacientes -> Contacto Emergencia (uno a uno)
- Pacientes -> Historial Médico (uno a uno)
- Pacientes -> Signos Vitales (uno a muchos)
```

### 5. Citas
```
Campos Necesarios:
- Citas:
  * id
  * patient_id
  * doctor_id
  * clinic_id
  * date_time
  * duration
  * type
  * reason
  * notes
  * status
  * created_at
  * updated_at

- Historial de Estados:
  * appointment_id
  * status
  * changed_at
  * changed_by
  * notes

Relaciones:
- Citas -> Pacientes (muchos a uno)
- Citas -> Doctores (muchos a uno)
- Citas -> Clínicas (muchos a uno)
- Citas -> Historial Estados (uno a muchos)
```

### 6. Quirófanos
```
Campos Necesarios:
- Cirugías:
  * id
  * patient_id
  * main_surgeon_id
  * assistant_surgeons (array)
  * date_time
  * estimated_duration
  * surgery_type
  * operating_room
  * anesthesia_type
  * required_equipment (array)
  * preop_notes
  * status
  * created_at
  * updated_at

- Equipo Quirúrgico:
  * surgery_id
  * user_id
  * role
  * notes

- Preparaciones:
  * surgery_id
  * description
  * completed
  * completed_at
  * completed_by

- Riesgos:
  * surgery_id
  * description
  * severity
  * mitigation_plan

Relaciones:
- Cirugías -> Pacientes (muchos a uno)
- Cirugías -> Doctores (muchos a muchos)
- Cirugías -> Equipo Quirúrgico (uno a muchos)
- Cirugías -> Preparaciones (uno a muchos)
- Cirugías -> Riesgos (uno a muchos)
```

### 7. Medicamentos
```
Campos Necesarios:
- Medicamentos:
  * id
  * commercial_name
  * generic_name
  * manufacturer
  * barcode
  * category
  * presentation
  * concentration
  * administration_route
  * requires_prescription
  * controlled
  * current_stock
  * min_stock
  * max_stock
  * location
  * expiration_date
  * lot_number
  * unit_cost
  * sale_price
  * status
  * created_at
  * updated_at

- Movimientos de Inventario:
  * id
  * medication_id
  * type (entrada/salida)
  * quantity
  * previous_stock
  * new_stock
  * reference_type
  * reference_id
  * notes
  * performed_by
  * created_at

Relaciones:
- Medicamentos -> Movimientos (uno a muchos)
- Medicamentos -> Categorías (muchos a uno)
```

### 8. Exámenes
```
Campos Necesarios:
- Exámenes:
  * id
  * patient_id
  * requesting_doctor_id
  * exam_type
  * scheduled_date
  * laboratory_area
  * preparation_required
  * notes
  * status
  * created_at
  * updated_at

- Resultados:
  * exam_id
  * performed_date
  * reported_by
  * values (array de parámetros y resultados)
  * interpretation
  * attachments (array)
  * status
  * created_at

Relaciones:
- Exámenes -> Pacientes (muchos a uno)
- Exámenes -> Doctores (muchos a uno)
- Exámenes -> Resultados (uno a uno)
```

### 9. Facturación
```
Campos Necesarios:
- Facturas:
  * id
  * invoice_number
  * patient_id
  * issue_date
  * due_date
  * items (array)
  * subtotal
  * tax
  * total
  * payment_status
  * payment_method
  * notes
  * created_at
  * updated_at

- Items Facturados:
  * invoice_id
  * type (consulta/procedimiento/medicamento/examen)
  * description
  * quantity
  * unit_price
  * total

- Pagos:
  * invoice_id
  * amount
  * payment_date
  * payment_method
  * reference_number
  * notes
  * created_at

Relaciones:
- Facturas -> Pacientes (muchos a uno)
- Facturas -> Items (uno a muchos)
- Facturas -> Pagos (uno a muchos)
```

### 10. Usuarios
```
Campos Necesarios:
- Usuarios:
  * id
  * email
  * name
  * role
  * status
  * last_login
  * created_at
  * updated_at

- Permisos:
  * user_id
  * resource
  * actions (array)
  * restrictions (json)

- Sesiones:
  * user_id
  * token
  * ip_address
  * user_agent
  * expires_at
  * created_at

Relaciones:
- Usuarios -> Permisos (uno a muchos)
- Usuarios -> Sesiones (uno a muchos)
```

### 11. Configuración del Sistema
```
Campos Necesarios:
- Configuración General:
  * id
  * institution_name
  * logo_url
  * timezone
  * language
  * currency
  * date_format
  * number_format
  * updated_at

- Configuración de Seguridad:
  * password_policy (json)
  * session_timeout
  * max_login_attempts
  * two_factor_enabled
  * allowed_ips (array)

- Configuración de Notificaciones:
  * email_enabled
  * sms_enabled
  * push_enabled
  * templates (array)
  * reminder_schedules (array)

- Configuración de Respaldos:
  * frequency
  * location
  * retention_days
  * encryption_enabled
  * auto_restore

- Configuración de Facturación:
  * invoice_series
  * tax_rate
  * payment_methods (array)
  * payment_terms (array)
  * document_templates (array)

Relaciones:
- Todas las configuraciones se relacionan con la tabla principal de configuración
``` 