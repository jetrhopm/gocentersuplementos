# Go Center Suplementos

Tienda virtual Laravel para venta de proteinas, suplementos, accesorios y ropa deportiva. Incluye catalogo publico, carrito por sesion, flujo de pago, pago por transferencia, preparacion de Clip Checkout, webhook idempotente, panel admin y datos demo.

## Stack

- Laravel 12, PHP 8.2+
- MySQL / MariaDB
- Blade
- TailwindCSS 4
- Alpine.js
- Lucide icons

## Instalacion local

1. Instala dependencias:

```bash
composer install
npm install
```

2. Crea tu archivo de entorno:

```bash
cp .env.example .env
php artisan key:generate
```

3. Configura `.env` con tus valores locales. No subas `.env` a GitHub.

Variables minimas:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tienda_fitness
DB_USERNAME=
DB_PASSWORD=

ADMIN_NAME=
ADMIN_EMAIL=
ADMIN_PASSWORD=
```

4. Crea la base de datos y corre migraciones:

```bash
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

URL local por XAMPP/Apache: `http://localhost/prote`.

URL alternativa si usas Artisan: `http://127.0.0.1:8000`.

Si usas XAMPP/Apache entrando por `http://localhost/prote`, la app tambien debe responder desde esa URL. La raiz del proyecto incluye `.htaccess` para redirigir internamente a `public/` y evitar listado de carpetas. La configuracion mas segura para hosting sigue siendo apuntar el DocumentRoot directamente a `public/`.

## Uso del administrador

El panel privado vive en:

```text
/admin
```

El usuario inicial se crea desde `ADMIN_EMAIL` y `ADMIN_PASSWORD` en `.env`.

Funciones incluidas:

- Dashboard con ventas del dia, ventas del mes, pedidos pendientes, stock bajo, mas vendidos y pedidos recientes.
- CRUD de productos con imagenes, categoria, precio, stock, destacado, activo/inactivo y variantes.
- CRUD de categorias.
- Gestion de pedidos: aprobar/marcar pagado, rechazar, preparar, enviar, entregar, cancelar, agregar guia y notas internas.
- Configuracion desde `/admin/configuracion`: tienda, envio, transferencia, Clip, correo, SEO, WhatsApp y modo mantenimiento.
- Exportacion CSV de pedidos.
- Vista imprimible de pedido.

## Flujo cliente

- Home con destacados y busqueda.
- Catalogo con filtros por categoria, marca, precio, talla y disponibilidad.
- URLs amigables: `/productos/{slug}`, `/categoria/{slug}`, `/ofertas`, `/checkout/pedido-recibido/{folio}`.
- Detalle de producto con imagenes, descuento, stock y variantes.
- Carrito persistente en sesion.
- Flujo de pago con validaciones de nombre, correo, telefono, direccion y CP.
- Consulta de pedido por folio + correo o telefono.

## Transferencia bancaria

Configura estos valores en `.env`:

```dotenv
BANK_NAME=
BANK_ACCOUNT_HOLDER=
BANK_ACCOUNT_NUMBER=
BANK_CLABE=
BANK_TRANSFER_INSTRUCTIONS=
```

Cuando el cliente elige transferencia:

- Se crea el pedido con estado `pendiente_transferencia`.
- Se muestran instrucciones de pago y folio.
- El cliente puede registrar referencia.
- El admin aprueba o rechaza manualmente.
- Al aprobar, el sistema marca como `pagado` y descuenta stock una sola vez.

## Clip Checkout

Configura:

```dotenv
CLIP_BASE_URL=https://api.payclip.com
CLIP_API_KEY=
CLIP_AUTH_SCHEME=Bearer
CLIP_WEBHOOK_SECRET=
CLIP_WEBHOOK_URL=
CLIP_SUCCESS_URL=
CLIP_ERROR_URL=
```

Notas:

- `CLIP_API_KEY` nunca se imprime en Blade ni JavaScript.
- Si Clip te entrega un encabezado completo como `Basic ...` o `Bearer ...`, puedes ponerlo completo en `CLIP_API_KEY`.
- Si Clip te entrega solo el token, usa `CLIP_AUTH_SCHEME` para indicar el prefijo.
- El servicio dedicado esta en `app/Services/ClipService.php`.
- El webhook esta en `POST /webhooks/clip`.
- En el panel admin puedes actualizar Clip desde `Configuracion > Clip`. Las claves sensibles se guardan en `.env` y no se muestran completas.

## Configuracion desde el panel

Ruta:

```text
/admin/configuracion
```

Secciones incluidas:

- General: nombre de tienda, URL, WhatsApp, stock bajo y limite de imagenes.
- Estilo visual: seleccion de tema publico entre Volt Lime, Ember Red y Glacier Cyan.
- Pagos: costo de envio, envio gratis y datos de transferencia.
- Clip: base URL, tipo de autorizacion, API key, webhook secret y URLs de retorno.
- Correo: mailer, SMTP, remitente y password.
- SEO y estado: descripcion por defecto y modo mantenimiento.

Los campos sensibles se dejan vacios para conservar el valor actual; al guardar se actualiza `.env` del lado servidor y se limpia cache de configuracion/rutas.

Flujo:

1. Se crea el pedido local con estado `pendiente_clip`.
2. Se crea un pago local con `external_reference` igual al folio.
3. Se solicita a Clip el link/intencion de checkout.
4. Se guardan `payment_request_id`, URL y respuesta segura.
5. El cliente se redirige a Clip.
6. El webhook valida pedido, monto, moneda, referencia, idempotencia y actualiza estado.

## Webhook local con ngrok

1. Levanta Laravel:

```bash
php artisan serve
```

2. En otra terminal:

```bash
ngrok http 8000
```

3. Copia la URL HTTPS publica y configura:

```dotenv
APP_URL=https://tu-url.ngrok-free.app
CLIP_WEBHOOK_URL=https://tu-url.ngrok-free.app/webhooks/clip
CLIP_SUCCESS_URL=https://tu-url.ngrok-free.app/pago/clip/exito
CLIP_ERROR_URL=https://tu-url.ngrok-free.app/pago/clip/error
```

4. Limpia cache de config:

```bash
php artisan config:clear
```

5. Crea un pedido con metodo Clip y revisa `payment_webhook_logs`.

## Estados de pedido

- `pendiente_transferencia`
- `pendiente_clip`
- `pagado`
- `rechazado`
- `preparando`
- `enviado`
- `entregado`
- `cancelado`
- `expirado`

## Seguridad antes de produccion

- Cambiar `APP_ENV=production`, `APP_DEBUG=false` y `APP_URL`.
- Generar una nueva `APP_KEY`.
- Cambiar credenciales DB, admin, banco, Clip y webhook.
- No subir `.env`, `vendor`, `node_modules`, `public/storage` ni archivos sensibles.
- Configurar HTTPS.
- Configurar `CLIP_WEBHOOK_SECRET` si Clip lo proporciona.
- Revisar permisos de `storage` y `bootstrap/cache`.
- Cambiar `ADMIN_PASSWORD` por una clave fuerte.
- Configurar correo real si se agregan notificaciones.
- Ejecutar `php artisan config:cache`, `route:cache` y `view:cache`.
- Validar backups de base de datos.
- Revisar politicas de privacidad, terminos, devoluciones y envios.

## Variables que deben cambiarse antes de hosting

- `APP_ENV`
- `APP_DEBUG`
- `APP_URL`
- `APP_KEY`
- `DB_HOST`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`
- `BANK_*`
- `CLIP_*`
- `STORE_WHATSAPP`
- `MAIL_*` si se configura correo

## Comandos utiles

```bash
composer install
npm install
npm run build
php artisan migrate --seed
php artisan storage:link
php artisan serve
php artisan test
```

## GitHub

El proyecto esta listo para repositorio con `.gitignore` protegiendo:

- `.env`
- `vendor`
- `node_modules`
- `public/build`
- `public/storage`
- logs y caches locales

Antes de subir, confirma que `.env.example` no tenga secretos reales.
