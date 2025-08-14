# Qué es HSL y HSLA

En diseño digital y desarrollo web, **HSL** y **HSLA** son formatos para definir colores basados en tres componentes: **Hue** (matiz), **Saturation** (saturación) y **Lightness** (luminosidad). HSLA añade un cuarto valor, **Alpha**, para controlar la transparencia.

## HSL (Hue, Saturation, Lightness)
- **Hue (matiz)**: Representa el color puro en un círculo cromático de 0 a 360 grados.
  - 0° = rojo
  - 120° = verde
  - 240° = azul
- **Saturation (saturación)**: Cantidad de color, expresada en porcentaje.
  - 0% = gris (sin color)
  - 100% = color pleno
- **Lightness (luminosidad)**: Claridad del color, expresada en porcentaje.
  - 0% = negro
  - 50% = color puro
  - 100% = blanco

**Sintaxis en CSS**: `hsl(hue, saturation%, lightness%)`

**Ejemplo**: `hsl(0, 100%, 50%)` → Rojo puro.

## HSLA (Hue, Saturation, Lightness, Alpha)
- Igual que HSL, pero añade el canal **Alpha** para opacidad.
- Alpha varía de 0 (transparente) a 1 (opaco).

**Sintaxis en CSS**: `hsla(hue, saturation%, lightness%, alpha)`

**Ejemplo**: `hsla(0, 100%, 50%, 0.5)` → Rojo puro con 50% de opacidad.

## Usos principales
- HSL facilita ajustar la saturación y luminosidad sin alterar el matiz.
- HSLA permite crear colores con transparencia para efectos y superposiciones.

## Ventajas sobre RGB/RGBA
- Más intuitivo para modificar tonos y variantes de un color.
- Ideal para generar paletas armónicas.

HSL y HSLA ofrecen un enfoque más visual y flexible para trabajar con colores en entornos digitales, especialmente en diseño web y UI.

