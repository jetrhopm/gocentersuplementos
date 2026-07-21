@extends('layouts.admin')

@section('title', 'Configuracion')

@php
    $value = fn (string $key, mixed $default = '') => old($key, $values[$key] ?? $default);
    $isSuperAdmin = auth()->user()?->isSuperAdmin();
@endphp

@section('content')
<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <span class="badge">Control de tienda</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">Configuracion</h1>
        <p class="mt-2 max-w-2xl text-sm text-zinc-400">Ajustes operativos de tienda, pagos, correo, SEO y mantenimiento sin editar archivos manualmente.</p>
    </div>
    <a href="{{ route('home') }}" class="btn-secondary">
        <i data-lucide="store" class="h-4 w-4"></i>
        Ver tienda
    </a>
</div>

<div class="mt-6 panel border-amber-400/30 bg-amber-400/10 p-4 text-sm text-amber-100">
    Las claves sensibles se guardan en el servidor y no se muestran completas. Deja esos campos vacios para conservar el valor actual.
</div>

<form id="admin-settings-form" method="POST" action="{{ route('admin.settings.update') }}" class="mt-6" x-data="{ tab: 'general' }">
    @csrf
    @method('PUT')

    <div class="panel p-3">
        <div class="grid gap-2 sm:grid-cols-2 {{ $isSuperAdmin ? 'lg:grid-cols-7' : 'lg:grid-cols-3' }}">
            <button type="button" class="settings-tab" :class="tab === 'general' ? 'settings-tab-active' : ''" x-on:click="tab = 'general'">
                General
            </button>
            <button type="button" class="settings-tab" :class="tab === 'carruseles' ? 'settings-tab-active' : ''" x-on:click="tab = 'carruseles'">
                Carruseles
            </button>
            <button type="button" class="settings-tab" :class="tab === 'pagos' ? 'settings-tab-active' : ''" x-on:click="tab = 'pagos'">
                Envio{{ $isSuperAdmin ? ' y transferencia' : '' }}
            </button>
            @if($isSuperAdmin)
            <button type="button" class="settings-tab" :class="tab === 'clip' ? 'settings-tab-active' : ''" x-on:click="tab = 'clip'">
                Clip
            </button>
            <button type="button" class="settings-tab" :class="tab === 'correo' ? 'settings-tab-active' : ''" x-on:click="tab = 'correo'">
                Correo
            </button>
            <button type="button" class="settings-tab" :class="tab === 'marketing' ? 'settings-tab-active' : ''" x-on:click="tab = 'marketing'">
                Marketing
            </button>
            @endif
            <button type="button" class="settings-tab" :class="tab === 'seo' ? 'settings-tab-active' : ''" x-on:click="tab = 'seo'">
                SEO y estado
            </button>
        </div>
    </div>

    <section class="panel mt-6 p-5" x-show="tab === 'general'" x-cloak>
        <h2 class="text-xl font-black uppercase text-white">General</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div class="field"><label>Nombre de la tienda</label><input name="APP_NAME" value="{{ $value('APP_NAME') }}" required></div>
            <div class="field"><label>URL principal</label><input name="APP_URL" value="{{ $value('APP_URL') }}" required></div>
            <div class="field"><label>WhatsApp</label><input name="STORE_WHATSAPP" value="{{ $value('STORE_WHATSAPP') }}" placeholder="5215512345678"></div>
            <div class="field"><label>Tamano maximo imagen KB</label><input type="number" name="STORE_MAX_UPLOAD_KB" value="{{ $value('STORE_MAX_UPLOAD_KB', 2048) }}" min="512"></div>
            <div class="field"><label>Stock bajo desde</label><input type="number" name="STORE_LOW_STOCK_THRESHOLD" value="{{ $value('STORE_LOW_STOCK_THRESHOLD', 5) }}" min="0"></div>
            <label class="md:col-span-2 flex items-center gap-3 rounded-md border border-zinc-800 bg-zinc-950 p-4">
                <input type="checkbox" name="STORE_HEADER_SHOW_TITLE" value="1" @checked(filter_var($value('STORE_HEADER_SHOW_TITLE', false), FILTER_VALIDATE_BOOL))>
                <span>
                    <span class="block font-bold text-white">Mostrar titulo en el encabezado</span>
                    <span class="mt-1 block text-sm text-zinc-500">Si lo desactivas, el encabezado usara el banner Go Center en lugar del texto para no chocar visualmente con el contenido.</span>
                </span>
            </label>
            <div class="md:col-span-2">
                <label>Estilo visual de la tienda</label>
                <div class="mt-3 grid gap-3 md:grid-cols-4">
                    @foreach([
                        'volt' => ['Volt Lime', 'Energia neon, alto contraste', '#bef264', '#22c55e'],
                        'ember' => ['Ember Red', 'Fuerza, intensidad y oferta', '#fb7185', '#f97316'],
                        'glacier' => ['Glacier Cyan', 'Premium, frio y tecnico', '#67e8f9', '#a3e635'],
                        'gocenter' => ['Go Center', 'Rojo, negro y blanco del perfil', '#e30613', '#f4f4f5'],
                    ] as $themeKey => [$themeName, $themeDescription, $primary, $secondary])
                        <label class="theme-option">
                            <input type="radio" name="STORE_THEME" value="{{ $themeKey }}" class="sr-only peer" @checked($value('STORE_THEME', 'volt') === $themeKey)>
                            <span class="theme-option-body">
                                <span class="flex gap-2">
                                    <span class="h-8 flex-1 rounded" style="background: {{ $primary }}"></span>
                                    <span class="h-8 flex-1 rounded" style="background: {{ $secondary }}"></span>
                                    <span class="h-8 flex-1 rounded bg-zinc-700"></span>
                                </span>
                                <span class="mt-3 block font-black text-white">{{ $themeName }}</span>
                                <span class="mt-1 block text-xs text-zinc-500">{{ $themeDescription }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="panel mt-6 p-5" x-show="tab === 'carruseles'" x-cloak>
        <h2 class="text-xl font-black uppercase text-white">Carruseles de la tienda</h2>
        <p class="mt-2 text-sm text-zinc-500">Escribe los slugs separados por coma. El orden que escribas sera el orden en la portada.</p>
        <div class="mt-5 grid gap-4">
            <div class="field">
                <label>Productos para el carrusel principal</label>
                <textarea name="STORE_HERO_CAROUSEL_SLUGS" rows="3" placeholder="combo-entrenamiento,super-pack,mega-combo">{{ $value('STORE_HERO_CAROUSEL_SLUGS', 'combo-entrenamiento,super-pack,mega-combo') }}</textarea>
            </div>
            <div class="field">
                <label>Productos para el carrusel de paquetes</label>
                <textarea name="STORE_PRODUCT_CAROUSEL_SLUGS" rows="4" placeholder="combo-entrenamiento,super-pack,paquete-completo,oferta-flash-amino-inlabs-5-piezas,super-combo">{{ $value('STORE_PRODUCT_CAROUSEL_SLUGS', 'combo-entrenamiento,super-pack,paquete-completo,oferta-flash-amino-inlabs-5-piezas,super-combo,combo-completo-azul') }}</textarea>
            </div>
            <div class="rounded-md border border-zinc-800 bg-zinc-950 p-4 text-sm text-zinc-400">
                Puedes copiar el slug desde el formulario de cada producto. Si un slug no existe o el producto esta inactivo, se omitira automaticamente.
            </div>
        </div>
    </section>

    <section class="panel mt-6 p-5" x-show="tab === 'pagos'" x-cloak>
        <h2 class="text-xl font-black uppercase text-white">Envio{{ $isSuperAdmin ? ' y transferencia' : '' }}</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div class="field"><label>Costo de envio</label><input name="STORE_SHIPPING_COST" value="{{ $value('STORE_SHIPPING_COST', 99) }}" inputmode="decimal"></div>
            <div class="field"><label>Envio gratis desde</label><input name="STORE_FREE_SHIPPING_FROM" value="{{ $value('STORE_FREE_SHIPPING_FROM', 1499) }}" inputmode="decimal"></div>
            @if($isSuperAdmin)
            <div class="field"><label>Banco</label><input name="BANK_NAME" value="{{ $value('BANK_NAME') }}"></div>
            <div class="field"><label>Titular</label><input name="BANK_ACCOUNT_HOLDER" value="{{ $value('BANK_ACCOUNT_HOLDER') }}"></div>
            <div class="field"><label>Cuenta</label><input name="BANK_ACCOUNT_NUMBER" value="{{ $value('BANK_ACCOUNT_NUMBER') }}"></div>
            <div class="field"><label>CLABE</label><input name="BANK_CLABE" value="{{ $value('BANK_CLABE') }}"></div>
            <div class="field md:col-span-2"><label>Instrucciones de transferencia</label><textarea name="BANK_TRANSFER_INSTRUCTIONS" rows="4">{{ $value('BANK_TRANSFER_INSTRUCTIONS') }}</textarea></div>
            @else
                <div class="md:col-span-2 rounded-md border border-zinc-800 bg-zinc-950 p-4 text-sm text-zinc-500">
                    Los datos bancarios solo pueden ser modificados por un super admin.
                </div>
            @endif
        </div>
    </section>

    @if($isSuperAdmin)
    <section class="panel mt-6 p-5" x-show="tab === 'clip'" x-cloak>
        <h2 class="text-xl font-black uppercase text-white">Pago con Clip</h2>
        <p class="mt-2 text-sm leading-6 text-zinc-500">Para el flujo normal solo necesitas la clave API y la clave secreta de Clip. El webhook es la URL que Clip usa para avisar cuando un pago se aprueba, rechaza, expira o falla.</p>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <input type="hidden" name="CLIP_BASE_URL" value="{{ $value('CLIP_BASE_URL', 'https://api.payclip.com') }}">
            <input type="hidden" name="CLIP_AUTH_SCHEME" value="{{ $value('CLIP_AUTH_SCHEME', 'Basic') ?: 'Basic' }}">
            <div class="field">
                <label>Clave API de Clip</label>
                <input name="CLIP_PUBLIC_KEY" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['CLIP_PUBLIC_KEY'] }}">
            </div>
            <div class="field">
                <label>Clave secreta de Clip</label>
                <input name="CLIP_SECRET_KEY" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['CLIP_SECRET_KEY'] }}">
            </div>
            <div class="field md:col-span-2"><label>Webhook URL</label><input name="CLIP_WEBHOOK_URL" value="{{ $value('CLIP_WEBHOOK_URL') }}"></div>
            <div class="md:col-span-2">
                <button
                    type="button"
                    class="btn-secondary min-h-11"
                    data-clip-test="{{ route('admin.settings.clip.test') }}"
                    data-clip-test-form="#admin-settings-form"
                    data-clip-test-result="#clip-test-result"
                >
                    <i data-lucide="plug-zap" class="h-4 w-4"></i>
                    Comprobar conexion
                </button>
                <div id="clip-test-result" class="mt-3 rounded-md border border-zinc-800 bg-zinc-950 p-3 text-sm text-zinc-500">
                    Usa este boton para validar las credenciales antes de guardar o publicar cambios.
                </div>
            </div>
            <details class="filter-details md:col-span-2 rounded-lg border border-zinc-800 bg-zinc-950 p-4">
                <summary class="filter-toggle">
                    <span><i data-lucide="settings-2" class="h-4 w-4"></i>Opciones avanzadas de Clip</span>
                    <i data-lucide="chevron-down" class="filter-chevron h-4 w-4 transition"></i>
                </summary>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div class="field"><label>Base URL</label><input name="CLIP_BASE_URL_VISIBLE" value="{{ $value('CLIP_BASE_URL', 'https://api.payclip.com') }}" disabled></div>
                    <div class="field"><label>Autorizacion</label><input value="{{ $value('CLIP_AUTH_SCHEME', 'Basic') ?: 'Basic' }}" disabled></div>
                    <div class="field md:col-span-2">
                        <label>Token legacy / Bearer opcional</label>
                        <input name="CLIP_API_KEY" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['CLIP_API_KEY'] }}">
                        <span class="text-xs text-zinc-500">Solo se usa si tu cuenta antigua de Clip no maneja clave API + secreta.</span>
                    </div>
                    <div class="field md:col-span-2">
                        <label>Webhook secret</label>
                        <input name="CLIP_WEBHOOK_SECRET" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['CLIP_WEBHOOK_SECRET'] }}">
                        <span class="text-xs text-zinc-500">Sirve para validar firmas si Clip te entrega un secreto de webhook. Si no lo tienes, puede quedar vacio.</span>
                    </div>
                    <div class="field"><label>Success URL</label><input name="CLIP_SUCCESS_URL" value="{{ $value('CLIP_SUCCESS_URL') }}"></div>
                    <div class="field"><label>Error URL</label><input name="CLIP_ERROR_URL" value="{{ $value('CLIP_ERROR_URL') }}"></div>
                </div>
            </details>
        </div>
    </section>

    <section class="panel mt-6 p-5" x-show="tab === 'correo'" x-cloak>
        <h2 class="text-xl font-black uppercase text-white">Correo</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div class="field"><label>Mailer</label><select name="MAIL_MAILER"><option value="log" @selected($value('MAIL_MAILER', 'log') === 'log')>Log</option><option value="smtp" @selected($value('MAIL_MAILER') === 'smtp')>SMTP</option><option value="array" @selected($value('MAIL_MAILER') === 'array')>Array/testing</option></select></div>
            @php
                $mailScheme = match(strtolower((string) $value('MAIL_SCHEME'))) {
                    'ssl', 'smtps' => 'smtps',
                    'tls', 'starttls', 'smtp' => 'smtp',
                    default => $value('MAIL_SCHEME'),
                };
            @endphp
            <div class="field">
                <label>Scheme / cifrado</label>
                <select name="MAIL_SCHEME">
                    <option value="">Sin cifrado / automatico</option>
                    <option value="smtps" @selected($mailScheme === 'smtps')>smtps - SSL, puerto 465</option>
                    <option value="smtp" @selected($mailScheme === 'smtp')>smtp - TLS/STARTTLS, puerto 587</option>
                </select>
            </div>
            <div class="field"><label>Host</label><input name="MAIL_HOST" value="{{ $value('MAIL_HOST', '127.0.0.1') }}"></div>
            <div class="field"><label>Puerto</label><input type="number" name="MAIL_PORT" value="{{ $value('MAIL_PORT', 2525) }}"></div>
            <div class="field"><label>Usuario</label><input name="MAIL_USERNAME" value="{{ $value('MAIL_USERNAME') }}"></div>
            <div class="field"><label>Password</label><input name="MAIL_PASSWORD" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['MAIL_PASSWORD'] }}"></div>
            <div class="field"><label>Correo remitente</label><input type="email" name="MAIL_FROM_ADDRESS" value="{{ $value('MAIL_FROM_ADDRESS', 'hello@example.com') }}"></div>
            <div class="field"><label>Nombre remitente</label><input name="MAIL_FROM_NAME" value="{{ $value('MAIL_FROM_NAME', '${APP_NAME}') }}"></div>
            <div class="field md:col-span-2">
                <label>Enviar prueba a</label>
                <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
                    <input type="email" name="test_email" value="{{ old('test_email', auth()->user()?->email) }}" placeholder="correo@dominio.com">
                    <button
                        type="button"
                        class="btn-secondary min-h-11"
                        data-mail-test="{{ route('admin.settings.mail.test') }}"
                        data-mail-test-form="#admin-settings-form"
                        data-mail-test-result="#mail-test-result"
                    >
                        <i data-lucide="mail-check" class="h-4 w-4"></i>
                        Probar correo
                    </button>
                </div>
                <div id="mail-test-result" class="mt-3 rounded-md border border-zinc-800 bg-zinc-950 p-3 text-sm text-zinc-500">
                    Envia un correo de prueba usando los valores visibles del formulario. Si el password queda vacio, se usa el guardado en el servidor.
                </div>
            </div>
        </div>
    </section>
    @endif

    @if($isSuperAdmin)
    <section class="panel mt-6 p-5" x-show="tab === 'marketing'" x-cloak>
        <h2 class="text-xl font-black uppercase text-white">Meta y Google</h2>
        <p class="mt-2 text-sm leading-6 text-zinc-500">Activa solo las integraciones que ya tengas verificadas. Los IDs publicos pueden mostrarse en el HTML; los tokens privados se conservan solo en el servidor.</p>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-black uppercase text-white">Meta Ads</h3>
                        <p class="mt-1 text-sm leading-6 text-zinc-500">Prepara Meta Pixel para audiencias, medicion de eventos y futuras conversiones de servidor.</p>
                    </div>
                    <i data-lucide="badge-dollar-sign" class="accent-text h-5 w-5"></i>
                </div>

                <label class="mt-4 flex items-start gap-3 rounded-md border border-zinc-800 bg-zinc-900/60 p-3">
                    <input type="checkbox" name="META_ADS_ENABLED" value="1" @checked(filter_var($value('META_ADS_ENABLED', false), FILTER_VALIDATE_BOOL))>
                    <span>
                        <span class="block font-bold text-white">Activar Meta Pixel</span>
                        <span class="mt-1 block text-xs leading-5 text-zinc-500">Inserta el pixel en la tienda publica y registra PageView.</span>
                    </span>
                </label>

                <div class="mt-4 grid gap-4">
                    <div class="field">
                        <label>Meta Pixel ID</label>
                        <input name="META_PIXEL_ID" value="{{ $value('META_PIXEL_ID') }}" inputmode="numeric" placeholder="Ej. 123456789012345">
                    </div>
                    <div class="field">
                        <label>Conversions API access token</label>
                        <input name="META_CAPI_ACCESS_TOKEN" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['META_CAPI_ACCESS_TOKEN'] }}">
                        <span class="text-xs leading-5 text-zinc-500">Opcional. Queda listo para enviar eventos servidor a servidor sin exponer el token al navegador.</span>
                    </div>
                    <div class="field">
                        <label>Test event code</label>
                        <input name="META_TEST_EVENT_CODE" value="{{ $value('META_TEST_EVENT_CODE') }}" placeholder="Opcional para pruebas de eventos">
                    </div>
                    <div>
                        <button
                            type="button"
                            class="btn-secondary min-h-11"
                            data-meta-test="{{ route('admin.settings.meta.test') }}"
                            data-meta-test-form="#admin-settings-form"
                            data-meta-test-result="#meta-test-result"
                        >
                            <i data-lucide="send" class="h-4 w-4"></i>
                            Probar Meta CAPI
                        </button>
                        <div id="meta-test-result" class="mt-3 rounded-md border border-zinc-800 bg-zinc-950 p-3 text-sm text-zinc-500">
                            Envia un evento de prueba a Meta usando el Pixel ID y token visibles. Si el token queda vacio, se usa el guardado en el servidor.
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-black uppercase text-white">Google</h3>
                        <p class="mt-1 text-sm leading-6 text-zinc-500">Verificacion para Search Console y etiqueta base para Google Ads.</p>
                    </div>
                    <i data-lucide="search-check" class="accent-text h-5 w-5"></i>
                </div>

                <label class="mt-4 flex items-start gap-3 rounded-md border border-zinc-800 bg-zinc-900/60 p-3">
                    <input type="checkbox" name="GOOGLE_SEARCH_ENABLED" value="1" @checked(filter_var($value('GOOGLE_SEARCH_ENABLED', false), FILTER_VALIDATE_BOOL))>
                    <span>
                        <span class="block font-bold text-white">Activar verificacion de Google</span>
                        <span class="mt-1 block text-xs leading-5 text-zinc-500">Agrega la meta etiqueta para reclamar el sitio en Search Console.</span>
                    </span>
                </label>

                <div class="mt-4 field">
                    <label>Google site verification</label>
                    <input name="GOOGLE_SITE_VERIFICATION" value="{{ $value('GOOGLE_SITE_VERIFICATION') }}" placeholder="Contenido del meta tag">
                </div>

                <label class="mt-4 flex items-start gap-3 rounded-md border border-zinc-800 bg-zinc-900/60 p-3">
                    <input type="checkbox" name="GOOGLE_ADS_ENABLED" value="1" @checked(filter_var($value('GOOGLE_ADS_ENABLED', false), FILTER_VALIDATE_BOOL))>
                    <span>
                        <span class="block font-bold text-white">Activar Google Ads tag</span>
                        <span class="mt-1 block text-xs leading-5 text-zinc-500">Carga gtag.js para medicion y conversiones publicitarias.</span>
                    </span>
                </label>

                <div class="mt-4 grid gap-4">
                    <div class="field">
                        <label>Google tag ID</label>
                        <input name="GOOGLE_TAG_ID" value="{{ $value('GOOGLE_TAG_ID') }}" placeholder="G-XXXX, GT-XXXX o AW-XXXX">
                    </div>
                    <div class="field">
                        <label>Conversion ID</label>
                        <input name="GOOGLE_ADS_CONVERSION_ID" value="{{ $value('GOOGLE_ADS_CONVERSION_ID') }}" placeholder="AW-XXXXXXXX">
                    </div>
                    <div class="field">
                        <label>Conversion label</label>
                        <input name="GOOGLE_ADS_CONVERSION_LABEL" value="{{ $value('GOOGLE_ADS_CONVERSION_LABEL') }}" placeholder="Opcional para evento de compra futuro">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 rounded-md border border-blue-400/20 bg-blue-400/10 p-4 text-sm leading-6 text-blue-100">
            Google Search no requiere API key para aparecer en buscadores: requiere sitio accesible, SEO correcto, sitemap y verificacion en Search Console. Meta Pixel tampoco usa API key publica; el token de Conversions API es privado y no debe salir en el frontend.
        </div>
    </section>
    @endif

    <section class="panel mt-6 p-5" x-show="tab === 'seo'" x-cloak>
        <h2 class="text-xl font-black uppercase text-white">SEO y estado</h2>
        <div class="mt-5 grid gap-4">
            <div class="field"><label>Descripcion SEO por defecto</label><textarea name="STORE_META_DESCRIPTION" rows="3">{{ $value('STORE_META_DESCRIPTION', 'Tienda fitness de proteinas, suplementos y ropa deportiva.') }}</textarea></div>
            <label class="flex items-center gap-3 rounded-md border border-zinc-800 bg-zinc-950 p-4">
                <input type="checkbox" name="STORE_MAINTENANCE_MODE" value="1" @checked(filter_var($value('STORE_MAINTENANCE_MODE', false), FILTER_VALIDATE_BOOL))>
                <span>
                    <span class="block font-bold text-white">Modo mantenimiento</span>
                    <span class="mt-1 block text-sm text-zinc-500">Bloquea la tienda publica y mantiene disponible el panel admin.</span>
                </span>
            </label>
        </div>
    </section>

    <div class="sticky bottom-0 mt-6 border-t border-zinc-800 bg-zinc-950/95 py-4 backdrop-blur">
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" form="admin-settings-form" class="btn-primary min-h-12">
                <i data-lucide="save" class="h-4 w-4"></i>
                Guardar configuracion
            </button>
        </div>
    </div>
</form>
@endsection
