# 📹 Sistema de Videollamadas - MediCare Pro

## Descripción General

El sistema de videollamadas de MediCare Pro permite realizar consultas médicas virtuales utilizando **Jitsi Meet** como plataforma de videollamadas. Esta funcionalidad está completamente integrada con el sistema de citas médicas.

## 🚀 Características Principales

- ✅ **Integración con Jitsi Meet** - Sin necesidad de servidores adicionales
- ✅ **Gestión completa de videollamadas** - Crear, iniciar, unirse y finalizar
- ✅ **Control de acceso por roles** - Doctores, pacientes y administradores
- ✅ **Verificación de dispositivos** - Prueba de cámara, micrófono y altavoces
- ✅ **Ventana de tiempo controlada** - Solo disponible en horarios apropiados
- ✅ **Seguimiento de participantes** - Registro de quién se une y cuándo
- ✅ **Historial de llamadas** - Duración, participantes y notas
- ✅ **Interfaz intuitiva** - Fácil de usar para médicos y pacientes

## 📋 Requisitos Previos

### Para Doctores y Administradores:
- Navegador web moderno (Chrome, Firefox, Safari, Edge)
- Cámara web funcional
- Micrófono funcional
- Conexión a internet estable
- Permisos de cámara y micrófono en el navegador

### Para Pacientes:
- Los mismos requisitos técnicos que los doctores
- Acceso al link de la videollamada (proporcionado por el doctor)

## 🔧 Configuración Inicial

### 1. Tipos de Citas
Para que una cita pueda tener videollamada, debe ser creada con el tipo:
- `video_consultation` - Consulta por videollamada
- `online` - Consulta online

### 2. Permisos de Usuario
Los permisos están configurados automáticamente:
- **Administradores**: Pueden gestionar todas las videollamadas
- **Doctores**: Pueden gestionar videollamadas de sus propias citas
- **Pacientes**: Pueden unirse a videollamadas de sus citas

## 📱 Cómo Usar el Sistema

### Para Doctores:

#### 1. Crear una Videollamada
1. Ve a la página de **Citas Médicas**
2. Busca una cita con tipo "video_consultation"
3. En la columna "Videollamada", haz clic en **"Crear"**
4. El sistema generará automáticamente la sala de videollamada

#### 2. Iniciar una Videollamada
1. En la página de citas, busca una cita con videollamada creada
2. Haz clic en **"Iniciar"** (solo disponible 15 min antes y 60 min después de la hora programada)
3. Se abrirá la página de preparación de videollamada
4. Verifica tus dispositivos (cámara, micrófono, altavoces)
5. Haz clic en **"Iniciar Videoconsulta"**

#### 3. Durante la Videollamada
- Usa los controles de Jitsi Meet para:
  - Activar/desactivar cámara y micrófono
  - Compartir pantalla
  - Chatear con el paciente
  - Grabar la sesión (si está habilitado)
- Para finalizar, haz clic en **"Finalizar Consulta"**

### Para Pacientes:

#### 1. Unirse a una Videollamada
1. Recibe el link de la videollamada del doctor
2. Haz clic en el link para acceder
3. Verifica tus dispositivos en la página de preparación
4. Espera a que el doctor inicie la consulta
5. Haz clic en **"Unirse a la Videoconsulta"**

#### 2. Durante la Videollamada
- Sigue las instrucciones del doctor
- Usa los controles básicos de Jitsi Meet
- Para salir, haz clic en **"Salir"**

## 🎯 Estados de Videollamada

### Estados Disponibles:
- **`pending`** - Videollamada creada, esperando inicio
- **`active`** - Videollamada en curso
- **`completed`** - Videollamada finalizada
- **`cancelled`** - Videollamada cancelada

### Indicadores Visuales:
- 🟡 **Pendiente** - Esperando inicio
- 🟢 **En curso** - Videollamada activa
- 🔵 **Completada** - Incluye duración
- 🔴 **Cancelada** - No disponible

## ⏰ Ventana de Tiempo

Las videollamadas solo están disponibles en ventanas de tiempo específicas:
- **15 minutos antes** de la hora programada
- **Hasta 60 minutos después** de la hora programada

Fuera de esta ventana, los botones estarán deshabilitados.

## 📊 Datos de Seguimiento

El sistema registra automáticamente:
- **Hora de inicio y fin** de la videollamada
- **Duración total** en minutos
- **Participantes** que se unieron
- **Roles** de cada participante (doctor/paciente)
- **Notas** de la consulta (opcional)

## 🔒 Seguridad y Privacidad

### Medidas de Seguridad:
- **Salas únicas** - Cada videollamada tiene un nombre de sala único
- **Control de acceso** - Solo usuarios autorizados pueden unirse
- **Ventana de tiempo** - Acceso limitado a horarios apropiados
- **Jitsi Meet** - Plataforma segura y confiable

### Privacidad:
- Las videollamadas no se graban por defecto
- Los datos de participantes se almacenan de forma segura
- Cumplimiento con regulaciones médicas de privacidad

## 🚨 Resolución de Problemas

### Problemas Comunes:

#### "No se puede acceder a la cámara"
- Verifica que el navegador tenga permisos de cámara
- Cierra otras aplicaciones que puedan estar usando la cámara
- Actualiza el navegador

#### "No se puede acceder al micrófono"
- Verifica los permisos de micrófono en el navegador
- Revisa la configuración de audio del sistema
- Prueba con diferentes navegadores

#### "La videollamada no está disponible"
- Verifica que estés dentro de la ventana de tiempo permitida
- Asegúrate de que la cita sea de tipo "video_consultation"
- Contacta al administrador si persiste el problema

#### "No puedo unirme a la videollamada"
- Verifica que el doctor haya iniciado la consulta
- Revisa tu conexión a internet
- Intenta refrescar la página

### Soporte Técnico:
- Revisa la consola del navegador para errores técnicos
- Contacta al administrador del sistema
- Documenta los pasos que llevaron al problema

## 📈 Métricas y Reportes

El sistema proporciona métricas sobre:
- Número de videollamadas realizadas
- Duración promedio de las consultas
- Participación de pacientes
- Problemas técnicos reportados

## 🔄 Actualizaciones Futuras

Funcionalidades planeadas:
- Grabación automática de consultas
- Integración con historiales médicos
- Notificaciones push para pacientes
- Sala de espera virtual
- Compartir archivos durante la consulta

## 📞 Contacto y Soporte

Para soporte técnico o preguntas sobre el sistema de videollamadas:
- Email: soporte@medicare-pro.com
- Documentación: [Ver documentación completa]
- Tickets: Sistema interno de tickets

---

## 🧪 Datos de Prueba

El sistema incluye datos de prueba para probar todas las funcionalidades:

### Citas de Videoconsulta Creadas:
- **Hoy 10:00** - Consulta de seguimiento (Pendiente)
- **Hoy 14:30** - Consulta general (Pendiente)  
- **Mañana 09:00** - Control médico virtual (Pendiente)
- **Ayer 11:00** - Consulta completada (25 min de duración)

### Usuarios de Prueba:
- **Doctor**: doctor.test@clinica.com / doctor123
- **Admin**: admin@clinica.com / admin123

¡El sistema está listo para usar! 🎉 