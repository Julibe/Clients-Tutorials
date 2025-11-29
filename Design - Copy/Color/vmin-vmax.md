# Qué son vmin y vmax en desarrollo web

En desarrollo web, **vmin** y **vmax** son unidades de medida relativas al tamaño del viewport que permiten adaptar elementos según el valor mínimo o máximo entre el ancho y la altura visibles del navegador.

## Cómo funciona
1. **vmin**: Representa un porcentaje del menor valor entre el ancho y la altura del viewport. `1vmin` equivale al 1% de la dimensión más pequeña.
2. **vmax**: Representa un porcentaje del mayor valor entre el ancho y la altura del viewport. `1vmax` equivale al 1% de la dimensión más grande.
3. **Adaptación dinámica**: Los valores se recalculan automáticamente al redimensionar la ventana o cambiar la orientación del dispositivo.

## Usos principales
- Crear elementos que mantengan proporciones en pantallas de diferentes orientaciones.
- Diseñar tipografías y componentes que crezcan o se reduzcan de forma equilibrada.
- Ajustar elementos visuales para que siempre ocupen un espacio proporcional en cualquier dispositivo.

## Ventajas
- Permiten diseños verdaderamente flexibles, adaptándose a cambios de ancho y alto.
- Útiles para componentes que deben mantener proporciones sin importar la orientación.

## Consideraciones
- En dispositivos móviles, la barra de navegación del navegador puede alterar los valores reales del viewport.
- Es recomendable combinarlos con otras unidades para mayor control y precisión.

Las unidades **vmin** y **vmax** son herramientas poderosas para el diseño responsive, asegurando que los elementos mantengan su escala proporcional en cualquier tamaño de pantalla.

