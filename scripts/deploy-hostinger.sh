#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-$PWD}"
DOMAIN="${DOMAIN:-gocentersuplementos.com.mx}"
PUBLIC_DIR="${APP_DIR}/public"
PUBLIC_HTML="${PUBLIC_HTML:-$HOME/domains/$DOMAIN/public_html}"
STAMP="$(date +%Y%m%d-%H%M%S)"

if [ ! -f "$APP_DIR/artisan" ]; then
    echo "ERROR: Ejecuta este script desde la raiz del proyecto Laravel."
    exit 1
fi

if [ ! -d "$PUBLIC_DIR" ]; then
    echo "ERROR: No existe la carpeta public de Laravel: $PUBLIC_DIR"
    exit 1
fi

mkdir -p "$PUBLIC_HTML"

if [ -f "$PUBLIC_HTML/index.php" ]; then
    cp "$PUBLIC_HTML/index.php" "$PUBLIC_HTML/index.php.bak-$STAMP"
fi

if [ -f "$PUBLIC_HTML/.htaccess" ]; then
    cp "$PUBLIC_HTML/.htaccess" "$PUBLIC_HTML/.htaccess.bak-$STAMP"
fi

cp "$PUBLIC_DIR/index.php" "$PUBLIC_HTML/index.php"
cp "$PUBLIC_DIR/.htaccess" "$PUBLIC_HTML/.htaccess"

mkdir -p "$PUBLIC_HTML/build" "$PUBLIC_HTML/assets"

if [ -d "$PUBLIC_DIR/build" ]; then
    cp -R "$PUBLIC_DIR/build/." "$PUBLIC_HTML/build/"
else
    echo "ADVERTENCIA: No existe public/build. Revisa si ya compilaste assets localmente."
fi

if [ -d "$PUBLIC_DIR/assets" ]; then
    cp -R "$PUBLIC_DIR/assets/." "$PUBLIC_HTML/assets/"
fi

for file in favicon.ico robots.txt; do
    if [ -f "$PUBLIC_DIR/$file" ]; then
        cp "$PUBLIC_DIR/$file" "$PUBLIC_HTML/$file"
    fi
done

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deploy publico terminado."
echo "APP_DIR: $APP_DIR"
echo "PUBLIC_HTML: $PUBLIC_HTML"
