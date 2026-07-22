# Go Center Suplementos

Tienda virtual Laravel para venta de proteinas, suplementos, accesorios y ropa deportiva. Incluye catalogo publico, carrito por sesion, flujo de pago, pago por transferencia, preparacion de Clip Checkout, webhook idempotente, panel admin y datos demo.

## Stack

- Laravel 12, PHP 8.2+
- MySQL / MariaDB
- Blade
- TailwindCSS 4
- Alpine.js
- Lucide icons

## Catalogo, imagenes y SQL versionados

El repositorio es privado y funciona como fuente completa del proyecto. Por decision del proyecto, se versionan codigo, assets publicos, imagenes de catalogo y SQL utiles para reinstalar la tienda.

Rutas principales:

- Marca e identidad: `public/assets/brand/`
- Imagenes de categorias: `public/assets/categories/`
- Productos Go Center: `public/assets/gocenter/products/`
- Productos Wolfpak/mochilas: `public/assets/wolfpak/products/`
- Assets compilados de Vite: `public/build/`
- SQL de carga/exportacion: `database/exports/`
- SQL base documentado: `database/base_datos_inicial/`

Aunque el repositorio sea privado, nunca se deben versionar `.env`, pedidos reales, datos de clientes, pagos reales, claves Clip, claves SMTP, datos bancarios reales ni respaldos completos de produccion.

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
CLIP_PUBLIC_KEY=
CLIP_SECRET_KEY=
CLIP_API_KEY=
CLIP_AUTH_SCHEME=Basic
CLIP_WEBHOOK_SECRET=
CLIP_WEBHOOK_URL=
CLIP_SUCCESS_URL=
CLIP_ERROR_URL=
```

Notas:

- `CLIP_PUBLIC_KEY` y `CLIP_SECRET_KEY` son el flujo normal de Clip Checkout. Se usan solo en servidor para generar la autorizacion `Basic`.
- `CLIP_API_KEY` queda como token legacy opcional si Clip te entrega una credencial antigua tipo `Bearer`.
- Ninguna clave de Clip se imprime en Blade ni JavaScript.
- El servicio dedicado esta en `app/Services/ClipService.php`.
- El webhook compatible con el proyecto anterior esta en `POST /pago/clip/webhook`. Tambien existe `POST /webhooks/clip`.
- Las URL de retorno compatibles son `/pago/clip/retorno/{folio}` y `/pago/clip/cancelado/{folio}`. Tambien existen `/pago/clip/exito?folio=...` y `/pago/clip/error?folio=...`.
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

## Meta Ads, Google y buscadores

La tienda tiene configuracion preparada para marketing desde el panel de super administrador.

- Meta Pixel puede activarse o desactivarse sin tocar codigo.
- Conversion API de Meta queda preparada del lado servidor cuando exista token.
- Google Search/SEO queda cubierto por titles, descriptions, rutas amigables, sitemap y robots.
- Google Ads queda preparado para IDs de medicion/conversion cuando se contraten campanas.

Si Meta o Google no se configuran, la tienda funciona normal. No se cargan scripts ni eventos de publicidad cuando estan desactivados o sin credenciales.

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
CLIP_SUCCESS_URL=
CLIP_ERROR_URL=
```

URL recomendada para pegar en Clip: `https://tu-url.ngrok-free.app/pago/clip/webhook`. Si prefieres usar la ruta nueva del proyecto, tambien funciona `https://tu-url.ngrok-free.app/webhooks/clip`.

Las URL de exito/error pueden quedar vacias. El sistema enviara automaticamente a Clip las rutas con folio del pedido: `/pago/clip/retorno/{folio}` y `/pago/clip/cancelado/{folio}`.

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

## Despliegue en Hostinger

En Hostinger, Laravel completo debe vivir fuera de `public_html`:

```text
/home/USUARIO/gocentersuplementos
```

`public_html` solo debe exponer archivos publicos y un `index.php` puente:

```text
/home/USUARIO/domains/DOMINIO/public_html
```

Actualizacion recomendada despues de hacer `git pull`:

```bash
cd ~/gocentersuplementos
git pull origin main
composer install --no-dev --optimize-autoloader

PUB="$HOME/domains/gocentersuplementos.com.mx/public_html"

rsync -a public/build/ "$PUB/build/"
rsync -a public/assets/ "$PUB/assets/"
rsync -a public/favicon.ico "$PUB/favicon.ico"
rsync -a public/robots.txt "$PUB/robots.txt"
rsync -a public/.htaccess "$PUB/.htaccess"

cat > "$PUB/index.php" <<'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = '/home/u705161084/gocentersuplementos';

if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $basePath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
PHP

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Cola de correos en produccion

Los correos de pedido nuevo, pago recibido y avisos al administrador se guardan en la cola para que el checkout no espere al servidor SMTP.

Verifica que el `.env` de produccion use:

```env
QUEUE_CONNECTION=database
```

Despues de migrar, la tabla `jobs` debe existir. Para procesar correos pendientes manualmente:

```bash
cd ~/gocentersuplementos
php artisan queue:work --stop-when-empty --tries=3 --timeout=60
```

En Hostinger conviene crear un cron cada minuto o cada pocos minutos con el mismo comando para que los correos salgan solos:

```bash
cd /home/u705161084/gocentersuplementos && /opt/alt/php83/usr/bin/php artisan queue:work --stop-when-empty --tries=3 --timeout=60
```

Si el hosting usa otra ruta de PHP, reemplaza `/opt/alt/php83/usr/bin/php` por la ruta que corresponda.

No uses `cp -R public/. public_html/` en actualizaciones normales porque puede sobrescribir el `index.php` especial de Hostinger.

Rollback rapido si un deploy rompe produccion:

```bash
cd ~/gocentersuplementos
git reset --hard COMMIT_BUENO

PUB="$HOME/domains/gocentersuplementos.com.mx/public_html"
rsync -a public/build/ "$PUB/build/"
rsync -a public/assets/ "$PUB/assets/"

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## GitHub

El proyecto esta listo para repositorio con `.gitignore` protegiendo:

- `.env`
- `vendor`
- `node_modules`
- `public/storage`
- logs y caches locales

El repositorio privado si puede incluir `public/build`, imagenes de productos y SQL de catalogo cuando el objetivo sea reinstalar la tienda completa en Hostinger.

Nota sobre `public/build`: la carpeta aparece en `.gitignore` para evitar ruido de builds locales, pero cuando se necesite subir una compilacion lista para Hostinger se puede versionar de forma intencional con:

```bash
git add -f public/build
```

Antes de subir, confirma que `.env.example` no tenga secretos reales.

### Subir a GitHub por primera vez

El proyecto ya esta en la rama `main` con un commit inicial. Para publicarlo en tu repositorio privado:

```bash
# 1. Crea un repositorio privado vacio en GitHub (sin README ni .gitignore).
# 2. Conecta tu repositorio (cambia USUARIO y REPO):
git remote add origin git@github.com:USUARIO/REPO.git
# o por HTTPS:
# git remote add origin https://github.com/USUARIO/REPO.git

# 3. Sube la rama main:
git push -u origin main
```

Notas:

- El remote `laravel-upstream` apunta al repositorio oficial de Laravel; no le hagas push.
- Recuerda recrear el `.env` en el servidor de produccion: el repositorio no lo incluye.
