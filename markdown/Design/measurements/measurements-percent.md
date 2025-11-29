# Qué es % (porcentaje)

En desarrollo web, **%** (porcentaje) es una unidad de medida relativa que representa un valor proporcional respecto a otra medida de referencia, normalmente el contenedor inmediato del elemento. Se utiliza ampliamente en CSS para crear diseños flexibles y adaptables.

## Cómo funciona
1. **Relativo al contenedor**: El porcentaje se calcula en base a las dimensiones del elemento padre o del área disponible.
2. **Ancho y alto**: En `width` y `height`, el valor en % se ajusta dinámicamente al tamaño del contenedor.
3. **Otras propiedades**: También puede usarse en márgenes, paddings, tipografía y posicionamiento, siempre tomando como referencia una dimensión determinada.

## Usos principales
- Crear diseños fluidos que se adapten a diferentes tamaños de pantalla.
- Definir anchos de columnas en sistemas de grid flexibles.
- Ajustar imágenes y videos para que escalen proporcionalmente.

## Ventajas
- Permite crear interfaces responsivas sin necesidad de valores fijos.
- Facilita la adaptación del diseño a dispositivos con diferentes resoluciones.

## Consideraciones
- El valor de referencia cambia según la propiedad en la que se utilice. Por ejemplo, el `height` en % puede depender de la altura definida del padre.
- Si el contenedor no tiene un tamaño explícito, el porcentaje puede no comportarse como se espera.

El uso de porcentajes es fundamental en el diseño web moderno, ya que permite crear interfaces adaptables que responden de forma natural a cambios en el tamaño de pantalla y contenido.

