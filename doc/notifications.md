# Configuración de Notificaciones y SMTP

PS Refer cuenta con un sistema de configuración de correo dinámico gestionable desde el Panel de Administración.

## 📧 Servidor SMTP Dinámico

A diferencia de un Laravel estándar que usa solo el archivo `.env`, PS Refer:
1. Lee la configuración desde la tabla `system_settings`    (llave: `smtp_config`).
2. El `AppServiceProvider` aplica estos valores al arrancar la aplicación (`bootDynamicSmtpConfiguration`).
3. Esto permite cambiar el servidor de correo (ej: de `test@comidayago.com` a otro) sin reiniciar el servidor ni tocar archivos de código.

## 🛠️ Probador de Conexión

En la vista de configuración:
- El botón **Probar Conexión** realiza un envío de correo real.
- Si el envío falla, reportará el error exacto (ej: `535 Incorrect authentication data`).

## ⚡ Colas de Trabajo (Queues)

Para evitar que el sistema se ralentice al enviar correos:
- Se utiliza la conexión `database`.
- Es necesario tener ejecutando el comando `php artisan queue:work` en producción (gestionado por un proceso como Supervisor).
- En entorno local de desarrollo, se puede cambiar `QUEUE_CONNECTION=sync` en el `.env` para pruebas inmediatas.

### 🔔 Alertas Implementadas
- **Nuevo Referido**: Notifica a los Admins y a los correos adicionales configurados en la Oferta.
