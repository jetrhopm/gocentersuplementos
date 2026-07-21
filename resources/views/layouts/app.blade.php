<!DOCTYPE html>
<html lang="es">
@php
    $marketing = config('services.marketing', []);
    $navCategories = \App\Models\Category::active()->orderBy('sort_order')->get();
    $cartCount = app(\App\Services\CartService::class)->count();
    $whatsapp = config('services.store.whatsapp');
    $theme = config('services.store.theme', 'volt');
    $storeName = config('app.name', 'Go Center Suplementos');
    $showHeaderTitle = config('services.store.header_show_title', false);
    $headerBanner = public_path('assets/brand/header-banner.jpg');
    $whatsappNumber = preg_replace('/\D+/', '', (string) $whatsapp);
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#09090b">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', config('services.store.meta_description'))">
    @if(($marketing['google_search_enabled'] ?? false) && filled($marketing['google_site_verification'] ?? null))
        <meta name="google-site-verification" content="{{ $marketing['google_site_verification'] }}">
    @endif
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="{{ asset('assets/brand/logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/brand/logo.jpg') }}">
    @if(($marketing['google_ads_enabled'] ?? false) && filled($marketing['google_tag_id'] ?? null))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($marketing['google_tag_id']) }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json($marketing['google_tag_id']));
            @if(filled($marketing['google_ads_conversion_id'] ?? null) && ($marketing['google_ads_conversion_id'] !== $marketing['google_tag_id']))
                gtag('config', @json($marketing['google_ads_conversion_id']));
            @endif
        </script>
    @endif
    @if(($marketing['meta_enabled'] ?? false) && filled($marketing['meta_pixel_id'] ?? null))
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', @json($marketing['meta_pixel_id']));
            fbq('track', 'PageView');
        </script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-950">
@if(($marketing['meta_enabled'] ?? false) && filled($marketing['meta_pixel_id'] ?? null))
    <noscript>
        <img height="1" width="1" style="display:none" alt=""
            src="https://www.facebook.com/tr?id={{ urlencode($marketing['meta_pixel_id']) }}&ev=PageView&noscript=1">
    </noscript>
@endif
<div class="site-shell theme-{{ $theme }} min-h-dvh w-full overflow-x-hidden pb-24 md:pb-0">
    <a href="#contenido" class="skip-link">Saltar al contenido</a>
    <header
        class="fixed inset-x-0 top-0 z-50 border-b border-zinc-800/80 bg-zinc-950/90 backdrop-blur"
        x-data="{ open: false }"
        x-on:click.outside="open = false"
        x-on:keydown.escape.window="open = false"
    >
        <div class="container-page header-grid h-16">
            <div class="header-left">
                <button class="btn-secondary px-3 lg:hidden" x-on:click="open = ! open" x-bind:aria-expanded="open" aria-controls="mobile-nav" aria-label="Menu">
                    <i data-lucide="menu" class="h-4 w-4"></i>
                </button>
            </div>
            <a href="{{ route('home') }}" class="site-brand header-brand">
                @if($showHeaderTitle || ! file_exists($headerBanner))
                    <span class="site-brand-name">
                        @if(str_contains(strtolower($storeName), 'go center'))
                        <span>Go Center</span>
                        <span>Suplementos</span>
                        @else
                        <span>{{ $storeName }}</span>
                        @endif
                    </span>
                @else
                    <span class="site-brand-banner">
                        <img src="{{ asset('assets/brand/header-banner.jpg') }}" alt="{{ $storeName }}" width="1280" height="426" fetchpriority="high">
                    </span>
                @endif
            </a>
            <nav class="header-nav hidden items-center justify-center gap-5 lg:flex">
                <a class="muted-link" href="{{ route('products.index') }}">Catalogo</a>
                @foreach($navCategories as $category)
                    <a class="muted-link" href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                @endforeach
            </nav>
            <div class="header-right flex items-center justify-end gap-2">
                <a href="{{ route('orders.lookup') }}" class="hidden muted-link sm:inline-flex">Consultar pedido</a>
                <a href="{{ route('cart.index') }}" class="btn-secondary relative px-3" aria-label="Carrito">
                    <i data-lucide="shopping-cart" class="h-4 w-4"></i>
                    <span class="accent-pill absolute -right-2 -top-2 grid h-5 min-w-5 place-items-center rounded-full px-1 text-xs font-black {{ $cartCount > 0 ? '' : 'hidden' }}" data-cart-count>{{ $cartCount }}</span>
                </a>
            </div>
        </div>
        <div id="mobile-nav" class="border-t border-zinc-800 bg-zinc-950 lg:hidden" x-show="open" x-transition>
            <nav class="container-page grid gap-1 py-4">
                <a class="muted-link py-2" href="{{ route('products.index') }}">Catalogo</a>
                @foreach($navCategories as $category)
                    <a class="muted-link py-2" href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                @endforeach
                <a class="muted-link py-2" href="{{ route('orders.lookup') }}">Consultar pedido</a>
            </nav>
        </div>
    </header>

    @if (session('status') || $errors->any())
        <div class="container-page pt-22">
            @if (session('status'))
                <div class="panel notice-success p-4 text-sm">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="panel border-red-500/40 bg-red-950/40 p-4 text-sm text-red-100">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>
    @endif

    <div class="toast-stack" data-toast-stack aria-live="polite" aria-atomic="true"></div>

    <main id="contenido" class="pt-16">
        @yield('content')
    </main>

    <section class="mt-20 border-t border-zinc-800 bg-zinc-950/60">
        <div class="container-page py-12">
            <div class="flex items-center gap-2">
                <i data-lucide="map-pin" class="accent-text h-5 w-5"></i>
                <h2 class="text-sm font-bold uppercase tracking-wide text-white">Nuestras sucursales</h2>
            </div>
            <p class="mt-2 text-sm text-zinc-400">Visitanos en Los Mochis y Guasave, Sinaloa.</p>

            <div class="mt-6 grid gap-6 md:grid-cols-2">
                <div class="overflow-hidden rounded-lg border border-zinc-800 bg-zinc-950">
                    <iframe
                        title="Mapa Go Center Suplementos Los Mochis"
                        src="https://www.google.com/maps?q=Go%20Center%20Suplementos%2C%20Av.%20Santos%20Degollado%20345%2C%20Centro%2C%2081200%20Los%20Mochis%2C%20Sin.&hl=es&z=16&output=embed"
                        class="h-64 w-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                    <div class="flex items-start justify-between gap-3 p-4">
                        <div>
                            <div class="font-bold text-white">Los Mochis</div>
                            <p class="mt-1 text-sm text-zinc-400">Av. Santos Degollado 345, Centro, 81200 Los Mochis, Sin.</p>
                        </div>
                        <a href="https://maps.app.goo.gl/ZJ1WprT2hUvnbfSN9" target="_blank" rel="noopener" class="btn-secondary min-h-10 shrink-0 text-sm">
                            <i data-lucide="navigation" class="h-4 w-4"></i>
                            Como llegar
                        </a>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg border border-zinc-800 bg-zinc-950">
                    <iframe
                        title="Mapa Go Center Suplementos Guasave"
                        src="https://www.google.com/maps?q=Go%20Center%20Suplementos%2C%20Blvd.%2016%20de%20Septiembre%2C%20Centro%2C%2081000%20Guasave%2C%20Sin.&hl=es&z=16&output=embed"
                        class="h-64 w-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                    <div class="flex items-start justify-between gap-3 p-4">
                        <div>
                            <div class="font-bold text-white">Guasave</div>
                            <p class="mt-1 text-sm text-zinc-400">Blvd. 16 de Septiembre, Centro, 81000 Guasave, Sin.</p>
                        </div>
                        <a href="https://maps.app.goo.gl/QKUWfJ73u1uCtVF5A" target="_blank" rel="noopener" class="btn-secondary min-h-10 shrink-0 text-sm">
                            <i data-lucide="navigation" class="h-4 w-4"></i>
                            Como llegar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="mt-0 border-t border-zinc-800 bg-zinc-950">
        <div class="container-page py-10">
            <div class="max-w-md">
                <div class="font-black uppercase tracking-normal text-white">{{ $storeName }}</div>
                <p class="mt-3 text-sm text-zinc-400">Suplementos deportivos, paquetes, proteinas y accesorios con pagos seguros y envios a todo Mexico.</p>
            </div>
            <div class="mt-8 flex flex-row justify-between gap-8">
                <div>
                    <div class="text-sm font-bold text-white">Tienda</div>
                    <div class="mt-3 grid gap-2">
                        <a class="muted-link" href="{{ route('products.index') }}">Productos</a>
                        <a class="muted-link" href="{{ route('orders.lookup') }}">Consulta de pedido</a>
                        <a class="muted-link" href="{{ route('cart.index') }}">Carrito</a>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-bold text-white">Politicas</div>
                    <div class="mt-3 grid gap-2">
                        <a class="muted-link" href="{{ route('policies.privacy') }}">Privacidad</a>
                        <a class="muted-link" href="{{ route('policies.terms') }}">Terminos</a>
                        <a class="muted-link" href="{{ route('policies.returns') }}">Devoluciones</a>
                        <a class="muted-link" href="{{ route('policies.shipping') }}">Envios</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @if($whatsappNumber && ! request()->routeIs('checkout.show'))
        <a
            href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hola, quiero informacion de Go Center Suplementos.') }}"
            target="_blank"
            rel="noopener"
            class="whatsapp-float fixed bottom-24 right-4 z-40 md:bottom-5 md:right-5"
            aria-label="Contactar por WhatsApp"
        >
            <svg aria-hidden="true" viewBox="0 0 32 32" class="h-5 w-5" fill="currentColor">
                <path d="M16.04 3.2A12.7 12.7 0 0 0 5.12 22.4L3.8 28.8l6.54-1.28A12.7 12.7 0 1 0 16.04 3.2Zm0 2.34a10.36 10.36 0 0 1 8.82 15.82 10.36 10.36 0 0 1-13.25 3.56l-.46-.23-3.88.76.78-3.78-.25-.48A10.36 10.36 0 0 1 16.04 5.54Zm-5.09 5.55c-.24.54-.74 1.58-.74 3.02 0 1.77 1.26 3.48 1.43 3.72.18.24 2.45 3.93 6.03 5.35 2.98 1.18 3.58.95 4.22.89.65-.06 2.1-.86 2.39-1.69.3-.83.3-1.54.21-1.69-.09-.15-.32-.24-.68-.42-.35-.18-2.1-1.04-2.43-1.16-.32-.12-.56-.18-.8.18-.24.35-.92 1.16-1.13 1.4-.21.24-.42.27-.77.09-.35-.18-1.49-.55-2.84-1.75-1.05-.94-1.76-2.1-1.97-2.45-.21-.36-.02-.55.16-.73.16-.16.35-.42.53-.63.18-.21.24-.36.36-.59.12-.24.06-.45-.03-.63-.09-.18-.8-1.93-1.1-2.64-.29-.7-.58-.6-.8-.61h-.68c-.24 0-.62.09-.95.45Z" />
            </svg>
        </a>
    @endif

    <nav class="mobile-bottom-nav md:hidden" aria-label="Navegacion rapida">
        <a href="{{ route('home') }}" class="mobile-bottom-link {{ request()->routeIs('home') ? 'active' : '' }}">
            <i data-lucide="house" class="h-5 w-5"></i>
            <span>Inicio</span>
        </a>
        <a href="{{ route('products.index', ['buscar' => 1]) }}" class="mobile-bottom-link {{ request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('offers') ? 'active' : '' }}">
            <i data-lucide="search" class="h-5 w-5"></i>
            <span>Buscar</span>
        </a>
        <a href="{{ route('orders.lookup') }}" class="mobile-bottom-link {{ request()->routeIs('orders.*') || request()->routeIs('checkout.received') ? 'active' : '' }}">
            <i data-lucide="package-check" class="h-5 w-5"></i>
            <span>Mis compras</span>
        </a>
        <a href="{{ route('cart.index') }}" class="mobile-bottom-link relative {{ request()->routeIs('cart.*') || request()->routeIs('checkout.show') ? 'active' : '' }}">
            <i data-lucide="shopping-cart" class="h-5 w-5"></i>
            <span>Carrito</span>
            <span class="accent-pill absolute right-3 top-1 grid h-4 min-w-4 place-items-center rounded-full px-1 text-[10px] font-black {{ $cartCount > 0 ? '' : 'hidden' }}" data-cart-count>{{ $cartCount }}</span>
        </a>
    </nav>
</div>
</body>
</html>
