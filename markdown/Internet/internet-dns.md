# Qué es DNS

DNS (Domain Name System) es el sistema que traduce los nombres de dominio legibles por humanos, como `example.com`, en direcciones IP que las computadoras utilizan para identificarse en la red. Funciona como la "agenda de contactos" de Internet.

## Cómo funciona
1. **Consulta de dominio**: El usuario ingresa un dominio en el navegador.
2. **Resolución**: El navegador solicita al servidor DNS la dirección IP correspondiente.
3. **Respuesta**: El servidor DNS devuelve la dirección IP y el navegador se conecta al servidor web.

## Componentes principales
- **Servidores raíz**: Primer nivel en la jerarquía DNS.
- **Servidores TLD**: Gestionan dominios de nivel superior como .com, .org o .net.
- **Servidores autoritativos**: Contienen la información específica del dominio.

## Usos principales
- Permitir que los usuarios accedan a sitios web usando nombres fáciles de recordar.
- Facilitar el cambio de servidores sin afectar la dirección del sitio.
- Redirigir tráfico a diferentes ubicaciones según configuraciones específicas.

## Ventajas
- Simplifica la navegación en Internet.
- Ofrece flexibilidad en la gestión de dominios y servicios.
- Permite configuraciones avanzadas como balanceo de carga y protección contra ataques.

## Consideraciones
- La propagación de cambios en DNS puede tardar desde minutos hasta 48 horas.
- Es un componente crítico; si falla, el dominio puede quedar inaccesible.

El DNS es una pieza fundamental de la infraestructura de Internet, ya que conecta los nombres de dominio con los servidores que alojan su contenido.

