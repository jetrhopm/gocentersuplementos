# Base de datos inicial

Esta carpeta es el unico lugar del repositorio donde deben guardarse archivos SQL versionados.

## Que si puede ir aqui

- SQL de estructura del proyecto: tablas, indices, llaves foraneas y datos tecnicos necesarios para iniciar.
- SQL de roles y administradores iniciales de prueba.
- SQL de configuracion basica no sensible, por ejemplo opciones publicas de la tienda, estados iniciales o valores visibles configurables.

## Que no debe ir aqui

- Productos reales o de catalogo.
- Imagenes de productos o rutas masivas de imagenes de productos.
- Pedidos, pagos, clientes, direcciones, telefonos, correos de compradores o historial de ventas.
- API keys, claves de Clip, claves SMTP, contrasenas reales, datos bancarios o cualquier secreto de produccion.
- Backups completos de produccion.

## Nombres recomendados

Usa nombres claros y numerados para que se entienda el orden de importacion:

```text
01_estructura.sql
02_roles_administradores.sql
03_configuracion_basica.sql
```

Si un archivo requiere ejecutarse solo en local, indicalo en el encabezado del SQL.

## Regla para catalogo

Los productos, imagenes de productos y SQL de carga de catalogo se manejan fuera de GitHub.
Pueden existir temporalmente en `database/exports` o en carpetas locales de trabajo, pero no deben subirse al repositorio.
