@extends('layouts.admin')

@section('title', 'Configuracion')

@php
    $value = fn (string $key, mixed $default = '') => old($key, $values[$key] ?? $default);
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
        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-6">
            <button type="button" class="settings-tab" :class="tab === 'general' ? 'settings-tab-active' : ''" x-on:click="tab = 'general'">
                General
            </button>
            <button type="button" class="settings-tab" :class="tab === 'carruseles' ? 'settings-tab-active' : ''" x-on:click="tab = 'carruseles'">
                Carruseles
            </button>
            <button type="button" class="settings-tab" :class="tab === 'pagos' ? 'settings-tab-active' : ''" x-on:click="tab = 'pagos'">
                Pagos
            </button>
            <button type="button" class="settings-tab" :class="tab === 'clip' ? 'settings-tab-active' : ''" x-on:click="tab = 'clip'">
                Clip
            </button>
            <button type="button" class="settings-tab" :class="tab === 'correo' ? 'settings-tab-active' : ''" x-on:click="tab = 'correo'">
                Correo
            </button>
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
        <h2 class="text-xl font-black uppercase text-white">Pagos y envio</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div class="field"><label>Costo de envio</label><input name="STORE_SHIPPING_COST" value="{{ $value('STORE_SHIPPING_COST', 99) }}" inputmode="decimal"></div>
            <div class="field"><label>Envio gratis desde</label><input name="STORE_FREE_SHIPPING_FROM" value="{{ $value('STORE_FREE_SHIPPING_FROM', 1499) }}" inputmode="decimal"></div>
            <div class="field"><label>Banco</label><input name="BANK_NAME" value="{{ $value('BANK_NAME') }}"></div>
            <div class="field"><label>Titular</label><input name="BANK_ACCOUNT_HOLDER" value="{{ $value('BANK_ACCOUNT_HOLDER') }}"></div>
            <div class="field"><label>Cuenta</label><input name="BANK_ACCOUNT_NUMBER" value="{{ $value('BANK_ACCOUNT_NUMBER') }}"></div>
            <div class="field"><label>CLABE</label><input name="BANK_CLABE" value="{{ $value('BANK_CLABE') }}"></div>
            <div class="field md:col-span-2"><label>Instrucciones de transferencia</label><textarea name="BANK_TRANSFER_INSTRUCTIONS" rows="4">{{ $value('BANK_TRANSFER_INSTRUCTIONS') }}</textarea></div>
        </div>
    </section>

    <section class="panel mt-6 p-5" x-show="tab === 'clip'" x-cloak>
        <h2 class="text-xl font-black uppercase text-white">Clip Checkout</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div class="field"><label>Base URL</label><input name="CLIP_BASE_URL" value="{{ $value('CLIP_BASE_URL', 'https://api.payclip.com') }}" required></div>
            <div class="field"><label>Tipo de autorizacion</label><select name="CLIP_AUTH_SCHEME"><option value="Basic" @selected($value('CLIP_AUTH_SCHEME', 'Basic') === 'Basic')>Basic</option><option value="Bearer" @selected($value('CLIP_AUTH_SCHEME') === 'Bearer')>Bearer</option></select></div>
            <div class="field">
                <label>Clave API de Clip</label>
                <input name="CLIP_PUBLIC_KEY" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['CLIP_PUBLIC_KEY'] }}">
            </div>
            <div class="field">
                <label>Clave secreta de Clip</label>
                <input name="CLIP_SECRET_KEY" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['CLIP_SECRET_KEY'] }}">
            </div>
            <div class="field md:col-span-2">
                <label>Token legacy / Bearer opcional</label>
                <input name="CLIP_API_KEY" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['CLIP_API_KEY'] }}">
            </div>
            <div class="field md:col-span-2">
                <label>Webhook secret</label>
                <input name="CLIP_WEBHOOK_SECRET" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['CLIP_WEBHOOK_SECRET'] }}">
            </div>
            <div class="field md:col-span-2"><label>Webhook URL</label><input name="CLIP_WEBHOOK_URL" value="{{ $value('CLIP_WEBHOOK_URL') }}"></div>
            <div class="field"><label>Success URL</label><input name="CLIP_SUCCESS_URL" value="{{ $value('CLIP_SUCCESS_URL') }}"></div>
            <div class="field"><label>Error URL</label><input name="CLIP_ERROR_URL" value="{{ $value('CLIP_ERROR_URL') }}"></div>
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
        </div>
    </section>

    <section class="panel mt-6 p-5" x-show="tab === 'correo'" x-cloak>
        <h2 class="text-xl font-black uppercase text-white">Correo</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div class="field"><label>Mailer</label><select name="MAIL_MAILER"><option value="log" @selected($value('MAIL_MAILER', 'log') === 'log')>Log</option><option value="smtp" @selected($value('MAIL_MAILER') === 'smtp')>SMTP</option><option value="array" @selected($value('MAIL_MAILER') === 'array')>Array/testing</option></select></div>
            <div class="field"><label>Scheme</label><input name="MAIL_SCHEME" value="{{ $value('MAIL_SCHEME') }}" placeholder="tls, ssl o null"></div>
            <div class="field"><label>Host</label><input name="MAIL_HOST" value="{{ $value('MAIL_HOST', '127.0.0.1') }}"></div>
            <div class="field"><label>Puerto</label><input type="number" name="MAIL_PORT" value="{{ $value('MAIL_PORT', 2525) }}"></div>
            <div class="field"><label>Usuario</label><input name="MAIL_USERNAME" value="{{ $value('MAIL_USERNAME') }}"></div>
            <div class="field"><label>Password</label><input name="MAIL_PASSWORD" value="" autocomplete="new-password" placeholder="Actual: {{ $masked['MAIL_PASSWORD'] }}"></div>
            <div class="field"><label>Correo remitente</label><input type="email" name="MAIL_FROM_ADDRESS" value="{{ $value('MAIL_FROM_ADDRESS', 'hello@example.com') }}"></div>
            <div class="field"><label>Nombre remitente</label><input name="MAIL_FROM_NAME" value="{{ $value('MAIL_FROM_NAME', '${APP_NAME}') }}"></div>
        </div>
    </section>

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
