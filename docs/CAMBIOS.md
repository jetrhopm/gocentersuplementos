# Registro de cambios — Go Center Suplementos

## Actualizacion 2026-07-14: pagos pendientes, consulta de pedido, datos visibles y control de superadmin

**Objetivo:** cerrar problemas detectados en pruebas locales antes de enviar a produccion: pedidos Clip pendientes que necesitaban reintento, consulta publica que podia caer en 419 por CSRF vencido, vista de pedido recibido incompleta para el comprador, eliminacion controlada de pedidos y login admin con datos precargados.

**Cambios funcionales:**
- `CheckoutController`: agrega flujo para retomar un pago Clip pendiente desde una ruta firmada. Si el pedido ya esta pagado o ya no es pagable, redirige al detalle publico con mensaje.
- `checkout.received`: muestra boton "Pagar con Clip" cuando el pedido sigue pendiente y agrega panel "Datos del comprador" con nombre, correo, telefono, codigo postal, direccion y referencias.
- `StoreController` + `routes/web.php` + `orders.lookup`: la consulta de pedido pasa de `POST /consultar-pedido` a `GET /consultar-pedido/buscar`, manteniendo validacion y throttling. Esto evita `419 Page Expired` por tokens CSRF vencidos en navegadores moviles.
- `Admin\OrderController`: agrega envio de recordatorio de pago por correo para pedidos pendientes y eliminacion de pedidos solo para superadmin.
- `OrderService`: agrega `deleteWithInventoryRestore()`. Si el pedido ya desconto inventario (`stock_discounted_at`), restaura stock de producto/variante antes de eliminar y registra movimiento `order_deleted_restore`.
- `Order`: agrega `isPayable()` para centralizar estados que aun pueden pagarse en linea.
- `PaymentReminderMail`: correo con enlace firmado para que el cliente vea su pedido y pueda pagar sin capturar folio/correo.
- `admin.orders.index` y `admin.orders.show`: muestran accion de eliminar pedido solo al superadmin, con confirmacion explicita.
- `admin.login`: elimina el correo precargado por defecto y agrega boton para ver/ocultar contrasena.

**Seguridad y reglas de negocio:**
- La eliminacion de pedidos se valida en backend con `isSuperAdmin()`. Ocultar el boton en Blade no es la unica proteccion.
- Admin normal recibe `403` si intenta borrar pedidos.
- Al borrar un pedido que nunca desconto stock, no se incrementa inventario.
- `order_items` y `payments` se eliminan por cascada; `payment_webhook_logs` e `inventory_movements` quedan como historial con `order_id` nulo segun las reglas de BD.
- La consulta publica por `GET` no modifica datos y conserva `throttle:12,1`.

**Pruebas agregadas o actualizadas:**
- Consulta de pedido por `GET` sin depender de sesion CSRF.
- Pedido recibido muestra datos del comprador.
- Cancelar Clip mantiene el pedido pendiente.
- Pedido pendiente muestra boton de pago.
- Admin puede enviar recordatorio de pago.
- Recordatorio bloqueado para pedido pagado.
- Superadmin puede eliminar pedido y restaurar stock descontado.
- Admin normal no puede eliminar pedidos.

**Validacion local:** `php artisan test` paso con 22 pruebas y 81 assertions.

---

Documento de referencia de los cambios realizados en esta sesión: **qué** se cambió, **por qué** y **dónde** (archivo y, cuando aplica, líneas/clases/funciones). Pensado para acompañar los commits y las descripciones de PR en GitHub.

Estado al momento de escribir:

| Commit | Descripción | Estado |
|--------|-------------|--------|
| `5a7e0ea8` | Ajustes de marketing + estilos de tienda | Subido |
| `0cbd2ef0` | Accesibilidad/UX (auditoría, parte 1) | Subido |
| `2cd9ebd9` | Accesibilidad/UX (auditoría, parte 2) | Subido |
| `a1d215ad` | Endurecimiento de seguridad | **Pendiente de push** |

> El push y la gestión en GitHub los realiza el propietario del repo. Aquí solo se documenta.

---

## 1. Carrusel Go Center: autoplay + control manual

**Por qué:** el carrusel de productos ("Carrusel Go Center") solo tenía botones; se pidió que avanzara solo con transición y que el usuario pudiera moverlo (arrastre/swipe).

**Dónde:**
- `resources/js/app.js` — bloque `document.querySelectorAll('[data-scroll-carousel]')`: se añadió avance automático con `setInterval` (vuelve al inicio al llegar al final), arrastre con mouse vía `pointerdown/move/up`, y pausa en hover/touch/drag. Un arrastre no dispara la navegación del enlace (bandera `moved` + `click` en captura).
- `resources/views/store/home.blade.php` — al contenedor `#home-products` se le agregó `data-scroll-carousel-interval="4500"`.
- `resources/css/app.css` — `.product-carousel` con `cursor: grab`; `.product-carousel.is-dragging` desactiva `scroll-snap` y selección; `.product-carousel img` sin arrastre fantasma.

---

## 2. Hero de la home: quitar bloque de texto y moverlo al carrusel

**Por qué:** el bloque de título/descripción sobre el hero ocupaba mucho alto y "empujaba" el contenido; se pidió ocultarlo e integrarlo como primera diapositiva del carrusel principal.

**Dónde (`resources/views/store/home.blade.php`):**
- Se eliminó la columna de texto izquierda del hero; el carrusel quedó a todo el ancho (`py-10 lg:py-14` en vez de `min-h-[78vh]`).
- La primera diapositiva de `$goCenterBanners` ahora lleva la identidad (nombre, descripción, dos CTAs "Comprar ahora" + "Ver packs"); se añadió soporte de `cta2`/`href2` en la plantilla del banner.

---

## 3. Mapas de sucursales (Google Maps embed)

**Por qué:** se pidió mostrar las sucursales (Los Mochis y Guasave) en el pie de página.

**Cómo:** se usó **Google Maps Embed** (`output=embed`), que **no requiere API key ni tarjeta**. Las direcciones se resolvieron de los enlaces cortos que proporcionó el propietario.

**Dónde (`resources/views/layouts/app.blade.php`):** nueva `<section>` "Nuestras sucursales" antes del `<footer>`, con dos `<iframe>` (`loading="lazy"`, `title`), dirección y botón "Cómo llegar".

Direcciones usadas:
- Los Mochis: Av. Santos Degollado 345, Centro, 81200.
- Guasave: Blvd. 16 de Septiembre, Centro, 81000.

---

## 4. Footer: quitar acceso admin y reordenar columnas

**Por qué:** se pidió eliminar el enlace visible al panel admin (quien administra ya conoce la ruta `/admin`) y dejar "Tienda" a la izquierda y "Políticas" a la derecha, también en móvil.

**Dónde (`resources/views/layouts/app.blade.php`):**
- Se eliminó la columna "Admin / Panel privado".
- Layout con `flex-row justify-between` (extremos en cualquier tamaño) y `text-right` en Políticas.

---

## 5. Agregar al carrito sin recargar + toast

**Por qué:** al agregar un producto la página recargaba y saltaba arriba; se pidió que se quedara en su lugar y mostrar un aviso tipo popup breve.

**Dónde:**
- `resources/js/app.js` — manejador `[data-cart-form]`: envía por `fetch` (JSON), actualiza `[data-cart-count]` y muestra `showToast()`. Función `showToast()` crea un toast temporal (~2.6 s) que se autooculta.
- `resources/views/layouts/app.blade.php` — contenedor `[data-toast-stack]` con `aria-live="polite"`; `data-cart-count` en el badge del carrito.
- `resources/views/partials/product-card.blade.php` y `resources/views/store/show.blade.php` — `data-cart-form` en los formularios de "Agregar".
- Backend ya devolvía JSON en `App\Http\Controllers\CartController@store` (`expectsJson`).

**Toast arriba con destello:** el toast se posicionó arriba (`.toast-stack { top: 4.75rem }`) con animación `toast-twinkle` (glow) + `toast-shine` (barrido) usando el color de acento del tema; respeta `prefers-reduced-motion`. (`resources/css/app.css`.)

---

## 6. Cupón sin recargar (mismo toast) + resumen en vivo

**Por qué:** aplicar/quitar cupón recargaba la página; se pidió el mismo comportamiento AJAX que agregar al carrito.

**Dónde:**
- `app/Http/Controllers/CartController.php` — `applyCoupon()`/`removeCoupon()` devuelven JSON con `totals` (formateados) y `coupon_html` cuando `expectsJson`. Nuevo método privado `cartJson()`.
- `resources/views/partials/cart-coupon.blade.php` — partial reutilizable del área de cupón (con `data-coupon-ajax`), renderizado por el servidor tras aplicar/quitar.
- `resources/views/cart/index.blade.php` — hooks `data-cart-subtotal/shipping/discount/total` y contenedor `data-coupon-area`.
- `resources/js/app.js` — manejador delegado `[data-coupon-ajax]`: actualiza totales, reemplaza el HTML del cupón y muestra toast.

---

## 7. Carrito: lista compacta + actualizar/eliminar por AJAX

**Por qué:** los productos del carrito se veían grandes (como la home); se pidió lista compacta con imagen chica y título "Productos en el carrito". Además el botón eliminar se cortaba en móvil, y actualizar/eliminar recargaba la página.

**Dónde:**
- `resources/views/cart/index.blade.php` — filas compactas (imagen 64×64, `truncate`), título "Productos en el carrito", controles con `w-full sm:w-auto` (bajan a segunda línea en móvil para no cortarse). Botón "Vaciar carrito" con confirmación.
- `resources/js/app.js` — manejador `[data-cart-line-form]`: actualizar/eliminar por `fetch`, actualiza totales, quita la fila del DOM y muestra estado vacío (`refreshCartEmptyState()`), sin recargar ni saltar arriba.
- `app/Http/Controllers/CartController.php` — `update()`/`destroy()` devuelven JSON (`cartJson`) cuando `expectsJson`.

---

## 8. Banner en carrito y checkout

**Por qué:** se pidió un banner/logo en carrito y checkout sin perder información ni verse amontonado.

**Dónde:**
- `resources/views/cart/index.blade.php` y `resources/views/checkout/show.blade.php` — bloque `.promo-banner` debajo del título (separado con `pt-8`).
- `resources/css/app.css` — `.promo-banner` (bordes, sombra, hover) e `img` con `width:100%; height:auto` para no deformar.

---

## 9. Auditoría de interfaz/accesibilidad (Web Interface Guidelines)

**Por qué:** se auditó la UI contra las guías de Vercel y se corrigieron los hallazgos. Commits `0cbd2ef0` y `2cd9ebd9`.

**Alta prioridad:**
- **Labels asociados + autocomplete** en todo el checkout (`for`/`id`, `autocomplete`, `spellcheck="false"` en email). `resources/views/checkout/show.blade.php`.
- **Skip link** "Saltar al contenido", **`<h1>`** en la home (primera diapositiva), `aria-expanded`/`aria-controls` en el botón de menú móvil. `resources/views/layouts/app.blade.php`, `resources/views/store/home.blade.php`.
- **Slides ocultos del carrusel con `inert`** para que no reciban foco de teclado. `resources/js/app.js` + `home.blade.php`.
- **Autoplay respeta `prefers-reduced-motion`** en ambos carruseles. `resources/js/app.js`.
- **`:focus-visible`** consistente (botones/enlaces/inputs) con el color de acento. `resources/css/app.css`.
- **Botón "Ir a pagar"** con `aria-disabled`/`tabindex="-1"` cuando el carrito está vacío. `resources/views/cart/index.blade.php`.

**Media/Baja:**
- `color-scheme: dark`, `<meta name="theme-color">`, `preconnect` a Unsplash, `touch-action: manipulation`, `-webkit-tap-highlight-color: transparent`, `tabular-nums` en totales, `…` tipográfica.
- `loading="lazy"` + `decoding="async"` en imágenes bajo el fold; `width`/`height` explícitos en banners (1280×720 / header 1280×426) y miniaturas (64×64) para evitar CLS; header con `fetchpriority="high"`.
- **Modal de zoom** con focus trap y retorno de foco al cerrar. `resources/views/store/show.blade.php`.
- **Combobox de colonias** navegable con flechas/Escape y roles ARIA (`role="combobox"/"listbox"/"option"`). `resources/views/checkout/show.blade.php` + `resources/js/app.js`.
- **Fuente Instrument Sans** ahora sí se carga (bunny.net, `display=swap` + preconnect). `resources/views/layouts/app.blade.php`.
- `overscroll-behavior: contain` en el modal de zoom. `resources/css/app.css`.

---

## 10. Endurecimiento de seguridad (commit `a1d215ad`, pendiente de push)

Basado en la revisión de seguridad. Solo se aplicaron las correcciones de código; los ajustes de entorno los realiza el propietario en el hosting.

### 10.1 Webhook de Clip: firma obligatoria por defecto (CRÍTICO)
**Por qué:** `validateSignature()` aceptaba webhooks **sin firma** en cualquier entorno que no fuera `production`. Como la ruta `POST /webhooks/clip` está exenta de CSRF y el `.env` local trae `APP_ENV=local`, un atacante podía forjar un webhook y **marcar pedidos como pagados** (fraude).

**Dónde:**
- `app/Services/ClipService.php` — `validateSignature()`: si no hay `CLIP_WEBHOOK_SECRET`, ahora **rechaza por defecto**; solo acepta sin firma si `CLIP_ALLOW_UNSIGNED_WEBHOOK=true` **y** no es producción (opt-in solo para pruebas).
- `config/services.php` — nueva clave `clip.allow_unsigned_webhook` (env `CLIP_ALLOW_UNSIGNED_WEBHOOK`, default `false`).
- `.env.example` — documenta `CLIP_ALLOW_UNSIGNED_WEBHOOK=false`.
- `phpunit.xml` — habilita el opt-in solo en pruebas para que `test_clip_legacy_webhook_marks_order_as_paid` siga pasando.

> Nota: se evaluó rechazar también los webhooks sin `amount`, pero el test demuestra que Clip envía eventos `REQUEST_COMPLETED` legítimos sin monto; como la firma ya cierra el riesgo, **no** se bloquean para no romper pagos reales.

### 10.2 Inyección en `.env` por saltos de línea (MEDIO)
**Por qué:** `formatValue()` escapaba comillas/backslashes pero no los saltos de línea; un admin podía inyectar líneas nuevas en el `.env` desde campos multilínea.

**Dónde:** `app/Services/EnvFileService.php` — `formatValue()` colapsa `\r\n`, `\r` y `\n` a un espacio antes de escribir.

### 10.3 Cabeceras de seguridad (BAJO/endurecimiento)
**Por qué:** faltaban cabeceras básicas de seguridad.

**Dónde:**
- `app/Http/Middleware/SecurityHeaders.php` — nuevo middleware: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `X-Permitted-Cross-Domain-Policies: none`, `Permissions-Policy`, y `Strict-Transport-Security` solo en HTTPS. (Sin CSP para no romper GTM/Meta Pixel/Alpine inline.)
- `bootstrap/app.php` — registrado en el grupo `web`.

### 10.4 Sesión segura (documentación)
**Dónde:** `.env.example` — se documentan `SESSION_SECURE_COOKIE` (true en producción con HTTPS) y `SESSION_SAME_SITE=lax`.

### 10.5 Limpieza
**Dónde:** se eliminó `resources/views/welcome.blade.php` (vista por defecto de Laravel, sin uso).

---

## 11. Retomar pago de Clip + recordatorio de pago por correo

**Por qué:** si el cliente cancela el pago en Clip, al revisar su pedido debe poder **retomar el pago**. Además, el admin/superadmin necesita **enviar un correo** al cliente con un enlace que abra su pedido (con toda la info y el botón de pago) **sin teclear folio ni correo**. Diseñado para sumar otras pasarelas en el futuro.

**Decisiones de diseño:**
- Al cancelar en Clip, el pedido **ya no se marca "cancelado"**: se mantiene pendiente para poder retomarlo. "Cancelado" queda para cuando el admin cancela.
- "Pagable" = estado `pendiente_clip`, `pendiente_transferencia` o `expirado` (y no pagado). Nuevo helper `Order::isPayable()`.
- La ruta de pago va **firmada** (`signed`), por lo que solo se accede desde la vista del pedido o el enlace del correo.

**Dónde:**
- `app/Models/Order.php` — `isPayable()`.
- `app/Http/Controllers/CheckoutController.php` — nuevo `pay(Order)` (retoma/inicia Clip para un pedido existente), helpers privados `startClipCheckout()` (reutilizado también por `store()`) y `ensurePayment()`; `clipCancelled()` ya **no** transiciona a cancelado.
- `routes/web.php` — `POST /pago/reintentar/{order}` (`checkout.pay`, `signed` + `throttle:12,1`); `POST /admin/pedidos/{order}/recordatorio-pago` (`admin.orders.payment-reminder`).
- `app/Http/Controllers/Admin/OrderController.php` — `sendPaymentReminder(Order)` (envía correo solo si el pedido es pagable).
- `app/Mail/PaymentReminderMail.php` + `resources/views/emails/orders/payment-reminder.blade.php` — correo con enlace **firmado** a `orders.public.show` (abre el pedido + botón de pago).
- `resources/views/checkout/received.blade.php` — panel "Termina tu pago" con botón "Pagar con Clip" cuando el pedido es pagable (se muestra también en la vista pública firmada del pedido).
- `resources/views/admin/orders/show.blade.php` — botón "Enviar recordatorio de pago" (con confirmación) cuando el pedido es pagable.

**Pruebas añadidas (`tests/Feature/StoreFlowTest.php`):** cancelar Clip mantiene el pedido pendiente; la vista del pedido muestra el botón de pago; el admin puede enviar el recordatorio; el recordatorio se bloquea en pedidos ya pagados. Total: **19/19**.

---

## Pendientes del propietario antes de producción (no es código)
- `APP_ENV=production`, `APP_DEBUG=false`.
- Definir `CLIP_WEBHOOK_SECRET` (y dejar `CLIP_ALLOW_UNSIGNED_WEBHOOK=false`).
- `SESSION_SECURE_COOKIE=true` con HTTPS.
- Cambiar la contraseña del admin (el seed usa `password`).

## Riesgos revisados y NO explotables (contexto)
- Sin SQLi (bindings de Eloquent), sin salidas `{!! !!}` sin escapar, sin `target="_blank"` sin `noopener` en vistas activas.
- Idempotencia del webhook (`payload_hash` + `event_id`), verificación de monto y moneda, y confirmación de pago **solo por webhook**.
- Descuento de stock e incremento de cupón en transacción con `lockForUpdate`.
- Autorización correcta en gestión de usuarios (`isSuperAdmin`) — sin escalada de privilegios.

---

## Verificación realizada
- `npm run build` sin errores.
- `php artisan test` → **15/15 pruebas** (56 aserciones).
- `php -l` sin errores de sintaxis en los archivos PHP modificados.
- Cabeceras de seguridad verificadas en vivo en `http://localhost/prote/`.
