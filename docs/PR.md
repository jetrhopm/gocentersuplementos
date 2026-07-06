# PR: Endurecimiento de seguridad + documentación de cambios

> Copia/pega este contenido en la descripción del Pull Request. Cubre los commits pendientes de subir (`a1d215ad`, `3828ba25`). Los commits de features y accesibilidad (`5a7e0ea8`, `0cbd2ef0`, `2cd9ebd9`) ya están en `main`.

## Título sugerido
`Endurecer seguridad (webhook Clip, .env, cabeceras) + documentación de cambios`

## Resumen
Corrige los hallazgos de código de la revisión de seguridad y agrega el registro de cambios de la sesión. El punto crítico: el webhook de Clip aceptaba peticiones **sin firma** fuera de producción, lo que permitía marcar pedidos como pagados de forma fraudulenta. También se cierra una vía de inyección en `.env` y se agregan cabeceras de seguridad.

## Cambios

### Seguridad (`a1d215ad`)
- **Webhook de Clip con firma obligatoria (CRÍTICO).** `validateSignature()` ahora rechaza por defecto si no hay `CLIP_WEBHOOK_SECRET`; solo acepta sin firma con el opt-in explícito `CLIP_ALLOW_UNSIGNED_WEBHOOK=true` y fuera de producción (solo pruebas).
  - `app/Services/ClipService.php`, `config/services.php`, `.env.example`, `phpunit.xml`
- **Anti-inyección en `.env` (MEDIO).** `EnvFileService::formatValue()` colapsa saltos de línea antes de escribir.
  - `app/Services/EnvFileService.php`
- **Cabeceras de seguridad.** Nuevo middleware global: `nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy`, `Permissions-Policy`, HSTS en HTTPS (sin CSP para no romper GTM/Meta Pixel/Alpine inline).
  - `app/Http/Middleware/SecurityHeaders.php`, `bootstrap/app.php`
- **Sesión segura (documentación).** `SESSION_SECURE_COOKIE`/`SESSION_SAME_SITE` en `.env.example`.
- **Limpieza.** Eliminada la vista por defecto `resources/views/welcome.blade.php`.

### Documentación (`3828ba25`)
- `docs/CAMBIOS.md`: registro completo de la sesión (qué/por qué/dónde).

## Cómo se probó
- `php artisan test` → **15/15 pruebas (56 aserciones)**.
- `npm run build` sin errores.
- `php -l` sin errores de sintaxis en los archivos PHP modificados.
- Cabeceras de seguridad verificadas en vivo en `http://localhost/prote/`.

## Notas de despliegue (acción del propietario, NO es código)
- [ ] `APP_ENV=production` y `APP_DEBUG=false`
- [ ] Definir `CLIP_WEBHOOK_SECRET` y dejar `CLIP_ALLOW_UNSIGNED_WEBHOOK=false`
- [ ] `SESSION_SECURE_COOKIE=true` (con HTTPS)
- [ ] Cambiar la contraseña del admin (el seed usa `password`)
- [ ] Ejecutar `php artisan config:cache route:cache view:cache` tras desplegar

## Checklist
- [x] Sin secretos en el diff (`.env` fuera del control de versiones)
- [x] Pruebas en verde
- [x] Sin cambios que rompan pagos legítimos de Clip (se revirtió el bloqueo por monto nulo)
- [x] Cambios documentados en `docs/CAMBIOS.md`
- [ ] Revisado por el propietario
- [ ] Variables de entorno de producción configuradas

## Riesgo / reversión
Bajo. Sin migraciones ni cambios de esquema. Para revertir: `git revert a1d215ad`. El opt-in `CLIP_ALLOW_UNSIGNED_WEBHOOK` permite reactivar el comportamiento anterior de webhook solo en entornos de prueba si fuera necesario.
