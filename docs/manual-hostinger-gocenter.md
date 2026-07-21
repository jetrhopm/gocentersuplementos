# Manual de despliegue en Hostinger

## Go Center Suplementos

Guia practica para instalar el proyecto Laravel desde un repositorio privado de GitHub en un hosting Hostinger con SSH, MySQL y dominio principal.

---

## 1. Objetivo

Este manual explica como subir el proyecto `gocentersuplementos` a otro hosting Hostinger desde cero, dejando Laravel instalado fuera de `public_html` y publicando unicamente los archivos publicos necesarios dentro de `public_html`.

La estructura final recomendada es:

```text
/home/USUARIO/gocentersuplementos
/home/USUARIO/domains/DOMINIO/public_html
```

Laravel queda en:

```text
/home/USUARIO/gocentersuplementos
```

La carpeta publica del dominio queda en:

```text
/home/USUARIO/domains/DOMINIO/public_html
```

---

## 2. Requisitos

- Cuenta Hostinger con SSH habilitado.
- Dominio agregado al hosting.
- Base de datos MySQL creada en Hostinger.
- Usuario MySQL con permisos sobre esa base.
- Repositorio privado en GitHub.
- Personal Access Token de GitHub para clonar el repositorio privado.
- PHP 8.3 o superior.
- Composer instalado en el servidor.

Node.js no es obligatorio en Hostinger si el proyecto ya sube los assets compilados en:

```text
public/build
```

El repositorio privado tambien puede incluir imagenes de productos y SQL de catalogo para reinstalar la tienda. Eso no cambia la regla de seguridad: `.env`, pedidos reales, datos de clientes, pagos reales, claves Clip, SMTP y datos bancarios reales nunca deben ir al repositorio.

---

## 3. Crear token de GitHub para repositorio privado

Para clonar un repositorio privado por HTTPS, GitHub pide usuario y token.

1. Entra a GitHub.
2. Ve a `Settings`.
3. Entra a `Developer settings`.
4. Entra a `Personal access tokens`.
5. Crea un token nuevo.
6. Si usas Fine-grained token, dale acceso solo al repositorio `gocentersuplementos`.
7. Permiso minimo recomendado:

```text
Contents: Read-only
```

8. Copia el token y guardalo en un lugar seguro.

Cuando Hostinger pida password al hacer `git clone`, pega el token. No pegues tu contrasena normal de GitHub.

---

## 4. Entrar por SSH desde Windows PowerShell

Abre PowerShell en Windows:

```text
Inicio > buscar PowerShell > Enter
```

Conectate al servidor:

```bash
ssh USUARIO@HOST_SSH
```

Ejemplo:

```bash
ssh u705161084@us-phx-web1543.hostinger.com
```

Si es la primera vez, acepta la huella del servidor escribiendo:

```bash
yes
```

Cuando veas algo parecido a esto, ya estas dentro:

```bash
[USUARIO@SERVIDOR ~]$
```

---

## 5. Verificar herramientas del servidor

Ejecuta:

```bash
php -v
composer -V
git --version
```

Si Node no existe, no pasa nada mientras `public/build` ya este en GitHub.

---

## 6. Clonar el proyecto

Desde la raiz del usuario:

```bash
cd ~
git clone https://github.com/jetrhopm/gocentersuplementos.git gocentersuplementos
cd ~/gocentersuplementos
```

Si el repo es privado, GitHub pedira:

```text
Username: tu_usuario_github
Password: tu_token_de_github
```

Verifica que se descargo:

```bash
ls -la
```

Debes ver archivos como:

```text
artisan
composer.json
app
bootstrap
config
database
public
resources
routes
storage
```

---

## 7. Instalar dependencias PHP

Dentro del proyecto:

```bash
cd ~/gocentersuplementos
composer install --no-dev --optimize-autoloader
```

---

## 8. Crear archivo .env

Copia el ejemplo:

```bash
cp .env.example .env
```

Edita el archivo:

```bash
nano .env
```

Valores minimos recomendados:

```env
APP_NAME="Go Center Suplementos"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://TU-DOMINIO.com.mx

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=NOMBRE_BASE_DATOS
DB_USERNAME=USUARIO_BASE_DATOS
DB_PASSWORD=PASSWORD_BASE_DATOS
```

Guarda en nano:

```text
Ctrl + O
Enter
Ctrl + X
```

Genera la llave de Laravel:

```bash
php artisan key:generate --force
```

---

## 9. Crear base de datos en Hostinger

En hPanel:

1. Entra a `Bases de datos`.
2. Crea una base MySQL.
3. Crea usuario y contrasena.
4. Asigna el usuario a la base.
5. Copia estos datos en `.env`.

Importante: el nombre real puede llevar prefijo del usuario de Hostinger, por ejemplo:

```text
u123456789_tienda_fitnes
u123456789_Gocenter
```

Usa exactamente los nombres que muestra Hostinger.

---

## 10. Crear tablas

Ejecuta:

```bash
cd ~/gocentersuplementos
php artisan migrate --force
```

Verifica:

```bash
php artisan migrate:status
```

---

## 11. Importar catalogo, productos, categorias y administradores

El proyecto incluye un SQL limpio para cargar catalogo y usuarios administrativos de prueba:

```text
database/exports/gocenter_catalog_admin_seed.sql
```

Tambien pueden existir SQL separados por categoria o cargas recientes dentro de `database/exports`. Antes de importar en produccion, confirma que el SQL no incluya pedidos reales, datos de compradores ni claves sensibles.

Importalo asi:

```bash
mysql -u USUARIO_BASE_DATOS -p NOMBRE_BASE_DATOS < database/exports/gocenter_catalog_admin_seed.sql
```

Ejemplo:

```bash
mysql -u u123456789_Gocenter -p u123456789_tienda_fitnes < database/exports/gocenter_catalog_admin_seed.sql
```

El sistema pedira la contrasena de MySQL.

Verifica productos y usuarios:

```bash
php artisan tinker --execute="echo 'Productos: '.App\Models\Product::count().PHP_EOL; echo 'Usuarios: '.App\Models\User::count().PHP_EOL;"
```

Debe mostrar productos y usuarios.

---

## 12. Preparar public_html

Busca la carpeta publica del dominio:

```bash
find ~/domains -maxdepth 3 -type d -name public_html
```

Ejemplo:

```text
/home/USUARIO/domains/gocentersuplementos.com.mx/public_html
```

Limpia la carpeta si el dominio esta nuevo:

```bash
rm -rf /home/USUARIO/domains/DOMINIO/public_html/*
```

Copia los archivos publicos de Laravel. En una instalacion nueva puedes copiar todo `public/.` porque todavia vas a corregir el `index.php` en el siguiente paso:

```bash
cp -R ~/gocentersuplementos/public/. /home/USUARIO/domains/DOMINIO/public_html/
```

---

## 13. Ajustar index.php de public_html

Este paso es obligatorio.

Edita:

```bash
nano /home/USUARIO/domains/DOMINIO/public_html/index.php
```

Reemplaza todo el contenido por:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = '/home/USUARIO/gocentersuplementos/storage/framework/maintenance.php')) {
    require $maintenance;
}

require '/home/USUARIO/gocentersuplementos/vendor/autoload.php';

/** @var Application $app */
$app = require_once '/home/USUARIO/gocentersuplementos/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

Cambia:

```text
USUARIO
DOMINIO
```

por los datos reales del hosting.

Ejemplo real:

```php
if (file_exists($maintenance = '/home/u705161084/gocentersuplementos/storage/framework/maintenance.php')) {
    require $maintenance;
}

require '/home/u705161084/gocentersuplementos/vendor/autoload.php';
$app = require_once '/home/u705161084/gocentersuplementos/bootstrap/app.php';
```

Guarda:

```text
Ctrl + O
Enter
Ctrl + X
```

---

## 14. Crear enlace de storage

Si Hostinger permite `storage:link`, usa:

```bash
php artisan storage:link
```

Si el servidor bloquea `exec`, crea el enlace manual:

```bash
rm -rf /home/USUARIO/domains/DOMINIO/public_html/storage
ln -s /home/USUARIO/gocentersuplementos/storage/app/public /home/USUARIO/domains/DOMINIO/public_html/storage
```

---

## 15. Permisos recomendados

Ejecuta:

```bash
chmod -R 775 storage bootstrap/cache
chmod 644 /home/USUARIO/domains/DOMINIO/public_html/index.php
chmod 644 /home/USUARIO/domains/DOMINIO/public_html/.htaccess
```

---

## 16. Limpiar y crear cache de produccion

Ejecuta:

```bash
cd ~/gocentersuplementos
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 17. Verificaciones rapidas

Verificar rutas de administradores:

```bash
php artisan route:list --path=admin/administradores
```

Verificar que existan assets:

```bash
ls -la /home/USUARIO/domains/DOMINIO/public_html/build/assets
```

Verificar que no haya archivos privados en `public_html`:

```bash
ls -la /home/USUARIO/domains/DOMINIO/public_html
```

Debe verse parecido a:

```text
assets
build
favicon.ico
.htaccess
index.php
robots.txt
storage
```

No deben quedar ahi:

```text
.env
vendor
app
database
resources
routes
storage/logs
composer.json
```

---

## 18. Accesos iniciales

Panel administrador:

```text
https://TU-DOMINIO.com.mx/admin
```

Super administrador:

```text
superadmin@local.test
password
```

Administrador limitado:

```text
admin@local.test
password
```

Cambia estas contrasenas antes de operar en produccion.

---

## 19. CRUD de administradores

Solo el super administrador puede gestionar usuarios administrativos:

```text
https://TU-DOMINIO.com.mx/admin/administradores
```

Desde ahi puede:

- Crear administradores.
- Editar administradores.
- Activar o desactivar administradores.
- Cambiar rol entre administrador y super administrador.

El administrador limitado no debe modificar APIs, cuentas bancarias, llaves de Clip ni administradores.

---

## 20. Configuracion de Clip y transferencia

Las llaves reales no deben ir en GitHub. Configuralas desde el panel administrador o directamente en produccion, segun el flujo activo del proyecto.

Variables recomendadas:

```env
CLIP_API_KEY=
CLIP_SECRET_KEY=
CLIP_WEBHOOK_SECRET=
CLIP_WEBHOOK_URL=https://TU-DOMINIO.com.mx/webhooks/clip
CLIP_SUCCESS_URL=https://TU-DOMINIO.com.mx/clip/success
CLIP_ERROR_URL=https://TU-DOMINIO.com.mx/clip/error
```

Webhook de Clip:

```text
https://TU-DOMINIO.com.mx/webhooks/clip
```

---

## 21. Actualizar el proyecto despues de cambios en GitHub

Cada vez que subas cambios al repositorio:

```bash
cd ~/gocentersuplementos
git pull origin main
composer install --no-dev --optimize-autoloader
```

Actualiza solo los archivos publicos necesarios. Esto evita pisar el `index.php` especial que apunta desde `public_html` hacia Laravel:

```bash
PUB="$HOME/domains/DOMINIO/public_html"

rsync -a public/build/ "$PUB/build/"
rsync -a public/assets/ "$PUB/assets/"
rsync -a public/favicon.ico "$PUB/favicon.ico"
rsync -a public/robots.txt "$PUB/robots.txt"
rsync -a public/.htaccess "$PUB/.htaccess"
```

Si el servidor no tiene `rsync`, usa `cp` carpeta por carpeta:

```bash
mkdir -p "$PUB/build" "$PUB/assets"
cp -R public/build/. "$PUB/build/"
cp -R public/assets/. "$PUB/assets/"
cp public/favicon.ico "$PUB/favicon.ico"
cp public/robots.txt "$PUB/robots.txt"
cp public/.htaccess "$PUB/.htaccess"
```

Despues asegura que `public_html/index.php` siga siendo el puente correcto:

```bash
cat > "$PUB/index.php" <<'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = '/home/USUARIO/gocentersuplementos';

if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $basePath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
PHP
```

Cambia `USUARIO` por el usuario real del hosting antes de pegarlo si estas preparando el comando fuera del servidor.

Luego:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 22. Soluciones preventivas

### La pagina carga sin diseno

Verifica que existan los assets:

```bash
ls -la /home/USUARIO/domains/DOMINIO/public_html/build/assets
```

Si no existen, copia de nuevo:

```bash
PUB="$HOME/domains/DOMINIO/public_html"
rsync -a ~/gocentersuplementos/public/build/ "$PUB/build/"
rsync -a ~/gocentersuplementos/public/assets/ "$PUB/assets/"
```

Despues revisa `index.php`. Debe apuntar a `/home/USUARIO/gocentersuplementos`, no a carpetas dentro de `public_html`.

### Imagenes no cargan

Verifica que los archivos existan fisicamente y que respondan con HTTP 200:

```bash
PUB="$HOME/domains/DOMINIO/public_html"
ls -la "$PUB/assets/brand"
ls -la "$PUB/assets/categories"
curl -I https://DOMINIO/assets/brand/logo.jpg
curl -I https://DOMINIO/assets/categories/packs-gocenter.jpg
```

Si falta una carpeta completa, sincroniza `public/assets`.

### La pagina se cae despues de actualizar

Primero revisa el `index.php` puente:

```bash
sed -n '1,35p' "$HOME/domains/DOMINIO/public_html/index.php"
```

Debe tener `$basePath = '/home/USUARIO/gocentersuplementos';`. Si aparece `__DIR__` apuntando a `vendor` o `bootstrap` dentro de `public_html`, vuelve a escribir el `index.php` del paso 21.

### Laravel muestra pagina en blanco o error 500

Revisa logs:

```bash
tail -n 80 ~/gocentersuplementos/storage/logs/laravel.log
```

Limpia cache:

```bash
php artisan optimize:clear
```

### Una ruta nueva no aparece

Actualiza codigo y limpia rutas:

```bash
git pull origin main
php artisan optimize:clear
php artisan route:list --path=admin/administradores
```

### Git no deja hacer pull por archivo modificado

Si el archivo modificado no contiene informacion importante:

```bash
git checkout -- RUTA_DEL_ARCHIVO
git pull origin main
```

### Storage link falla

Usa enlace manual:

```bash
rm -rf /home/USUARIO/domains/DOMINIO/public_html/storage
ln -s /home/USUARIO/gocentersuplementos/storage/app/public /home/USUARIO/domains/DOMINIO/public_html/storage
```

### Rollback rapido

Si una actualizacion rompe produccion, regresa al ultimo commit bueno:

```bash
cd ~/gocentersuplementos
git reset --hard COMMIT_BUENO

PUB="$HOME/domains/DOMINIO/public_html"
rsync -a public/build/ "$PUB/build/"
rsync -a public/assets/ "$PUB/assets/"

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Guarda siempre el hash del ultimo commit que funciono antes de actualizar.

---

## 23. Checklist final

- El dominio abre correctamente.
- El diseno carga con CSS.
- El panel `/admin` abre.
- El super administrador puede entrar.
- Los productos aparecen.
- Las imagenes cargan.
- El carrito funciona.
- El checkout funciona.
- El webhook de Clip apunta al dominio real.
- `.env` no esta dentro de `public_html`.
- `APP_DEBUG=false`.
- Las contrasenas de prueba ya fueron cambiadas.
- Las llaves reales de Clip y datos bancarios no estan en GitHub.

---

## 24. Comandos resumidos

```bash
cd ~
git clone https://github.com/jetrhopm/gocentersuplementos.git gocentersuplementos
cd ~/gocentersuplementos
composer install --no-dev --optimize-autoloader
cp .env.example .env
nano .env
php artisan key:generate --force
php artisan migrate --force
mysql -u USUARIO_DB -p NOMBRE_DB < database/exports/gocenter_catalog_admin_seed.sql
cp -R ~/gocentersuplementos/public/. /home/USUARIO/domains/DOMINIO/public_html/
nano /home/USUARIO/domains/DOMINIO/public_html/index.php
ln -s /home/USUARIO/gocentersuplementos/storage/app/public /home/USUARIO/domains/DOMINIO/public_html/storage
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 25. Nota importante

Cada hosting puede cambiar rutas, version de PHP y restricciones. La regla principal es:

- Laravel completo vive fuera de `public_html`.
- Solo el contenido de `public` vive dentro de `public_html`.
- `public_html/index.php` debe apuntar al proyecto real.
- `.env` nunca debe estar dentro de `public_html`.
- En actualizaciones normales, sincroniza `build`, `assets`, `favicon.ico`, `robots.txt` y `.htaccess`; no copies todo `public/.` si no vas a reescribir el `index.php`.
