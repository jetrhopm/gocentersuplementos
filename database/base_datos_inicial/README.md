# Base de datos inicial

Esta carpeta es el unico lugar del repositorio donde deben guardarse archivos SQL versionados.

El repositorio de este proyecto es privado. Por decision del proyecto, tambien se pueden versionar SQL de catalogo y assets de productos cuando sirvan para reinstalar la tienda completa en Hostinger.

## Que si puede ir aqui

- SQL de estructura del proyecto: tablas, indices, llaves foraneas y datos tecnicos necesarios para iniciar.
- SQL de roles y administradores iniciales de prueba.
- SQL de configuracion basica no sensible, por ejemplo opciones publicas de la tienda, estados iniciales o valores visibles configurables.
- SQL de catalogo inicial o por categoria, siempre que no incluya pedidos reales ni datos de compradores.
- Referencias a imagenes publicas versionadas en `public/assets`.

## Que no debe ir aqui

- Pedidos, pagos, clientes, direcciones, telefonos, correos de compradores o historial de ventas.
- API keys, claves de Clip, claves SMTP, contrasenas reales, datos bancarios o cualquier secreto de produccion.
- Backups completos de produccion.

## Nombres recomendados

Usa nombres claros y numerados para que se entienda el orden de importacion:

```text
01_estructura.sql
02_roles_administradores.sql
03_configuracion_basica.sql
04_catalogo_inicial.sql
```

Si un archivo requiere ejecutarse solo en local, indicalo en el encabezado del SQL.

## Regla para catalogo y produccion

Los productos e imagenes pueden subirse al repositorio privado cuando el objetivo sea reinstalar la tienda. Lo que no debe subirse nunca son datos generados por ventas reales.

Antes de agregar o actualizar un SQL, revisa que no contenga:

- Pedidos reales.
- Correos, telefonos o direcciones de clientes.
- Pagos, referencias bancarias o respuestas completas de pasarelas.
- Claves de Clip, correo, banco, paneles o tokens.
