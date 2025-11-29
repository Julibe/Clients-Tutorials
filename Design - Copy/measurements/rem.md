# Qué es rem

En desarrollo web, **rem** (root em) es una unidad de medida relativa en CSS que se basa en el tamaño de fuente del elemento raíz del documento, normalmente el elemento `<html>`. A diferencia de **em**, que es relativa al elemento padre, **rem** siempre se calcula en función del tamaño de fuente raíz, lo que la hace más predecible.

## Cómo funciona
1. **Elemento raíz**: Por defecto, la mayoría de navegadores establecen `font-size: 16px` en `<html>`.
2. **Cálculo**: `1rem` equivale al tamaño de fuente del elemento raíz. Si este es de 16px, entonces `1rem = 16px`.
3. **Escalabilidad global**: Cambiar el tamaño de fuente en `<html>` ajusta automáticamente todos los valores definidos en rem.

## Usos principales
- Definir tamaños de texto consistentes en todo el sitio.
- Establecer márgenes, paddings y dimensiones que escalen de forma proporcional.
- Crear sistemas de diseño flexibles y fáciles de mantener.

## Ventajas
- Coherencia en el tamaño, ya que no depende de elementos padres.
- Facilita la adaptación del diseño cambiando un solo valor en el elemento raíz.
- Mejora la accesibilidad al permitir escalado uniforme del contenido.

## Consideraciones
- Si el tamaño de fuente del elemento raíz cambia, todos los elementos que usan rem se ajustarán proporcionalmente.
- Es recomendable combinar rem para medidas globales y em para ajustes locales.

La unidad **rem** es una herramienta clave para mantener consistencia y control en diseños responsivos, permitiendo escalar la interfaz de manera eficiente y accesible.

