# Qué es el Protocolo SMTP

SMTP (Simple Mail Transfer Protocol) es un protocolo de comunicación utilizado para enviar correos electrónicos a través de Internet. Es el estándar para la transmisión de mensajes desde un cliente de correo hacia un servidor de correo o entre servidores.

## Cómo funciona
1. **Composición del mensaje**: El usuario redacta un correo en su cliente de correo electrónico.
2. **Envío al servidor SMTP**: El cliente envía el mensaje al servidor SMTP configurado.
3. **Transmisión**: El servidor SMTP reenvía el correo al servidor de destino, que lo almacenará para su entrega.
4. **Recepción**: El destinatario recupera el mensaje usando protocolos como IMAP o POP3.

## Usos principales
- Envío de correos electrónicos personales y corporativos.
- Transmisión de notificaciones automáticas desde aplicaciones o sitios web.
- Comunicación entre servidores de correo.

## Ventajas
- Estándar ampliamente soportado por todos los servicios de correo.
- Permite configurar autenticación para mayor seguridad.
- Compatible con cifrado mediante STARTTLS o SSL/TLS.

## Consideraciones
- SMTP solo se encarga del envío de mensajes, no de su recepción.
- Requiere configuración correcta de puertos y autenticación para evitar uso indebido (spam).

SMTP es fundamental para el funcionamiento del correo electrónico, garantizando que los mensajes salgan del remitente y lleguen a los servidores correspondientes para su entrega.

