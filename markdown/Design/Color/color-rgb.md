# Qué es RGB y RGBA

En diseño digital y desarrollo web, **RGB** y **RGBA** son formatos para definir colores basados en el modelo de color aditivo **Red, Green, Blue**. Se utilizan ampliamente en CSS, gráficos y otras aplicaciones para especificar colores de manera precisa.

## RGB (Red, Green, Blue)
- Representa colores como una combinación de luz roja, verde y azul.
- Cada canal de color se define con un valor entre 0 y 255:
  - 0 significa ausencia de luz en ese canal.
  - 255 significa máxima intensidad en ese canal.
- Sintaxis en CSS: `rgb(rojo, verde, azul)`.

**Ejemplo**: `rgb(255, 255, 255)`
- Rojo: 255 (máxima intensidad)
- Verde: 255 (máxima intensidad)
- Azul: 255 (máxima intensidad)
- Resultado: Blanco.

## RGBA (Red, Green, Blue, Alpha)
- Extiende RGB añadiendo un cuarto canal **Alpha**, que controla la opacidad.
- El valor alpha va de 0 a 1:
  - 0 = completamente transparente.
  - 1 = completamente opaco.
- Sintaxis en CSS: `rgba(rojo, verde, azul, alpha)`.

**Ejemplo**: `rgba(255, 0, 0, 0.5)`
- Rojo: 255 (máxima intensidad)
- Verde: 0
- Azul: 0
- Alpha: 0.5 (50% de opacidad)
- Resultado: Rojo semitransparente.

## Usos principales
- RGB: Definir colores sólidos en pantallas.
- RGBA: Crear efectos de transparencia y superposición.

## Consideraciones
- RGB y RGBA son ideales para medios digitales, ya que se basan en luz.
- El uso de valores de opacidad en RGBA permite crear diseños más dinámicos y con profundidad visual.

Tanto **RGB** como **RGBA** son esenciales para el diseño digital, proporcionando control total sobre la composición y la transparencia del color en entornos de pantalla.

