<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', config('services.store.meta_description'))">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-950">
@php
    $navCategories = \App\Models\Category::active()->orderBy('sort_order')->get();
    $cartCount = app(\App\Services\CartService::class)->count();
    $whatsapp = config('services.store.whatsapp');
    $theme = config('services.store.theme', 'volt');
    $storeName = config('app.name', 'Go Center Suplementos');
    $brandMark = str_contains(strtolower($storeName), 'go center') ? 'GO' : 'NP';
    $brandLogo = public_path('assets/gocenter/logo.jpg');
@endphp
<div class="site-shell theme-{{ $theme }} min-h-dvh w-full overflow-x-hidden pb-24 md:pb-0">
    <header class="fixed inset-x-0 top-0 z-50 border-b border-zinc-800/80 bg-zinc-950/90 backdrop-blur" x-data="{ open: false }">
        <div class="container-page flex h-16 items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="site-brand">
                @if(file_exists($brandLogo))
                    <span class="brand-mark brand-logo-frame">
                        <img src="{{ asset('assets/gocenter/logo.jpg') }}" alt="{{ $storeName }}" class="h-full w-full object-cover">
                    </span>
                @else
                    <span class="brand-mark brand-logo-frame grid place-items-center text-sm font-black">{{ $brandMark }}</span>
                @endif
                <span class="site-brand-name">
                    @if(str_contains(strtolower($storeName), 'go center'))
                        <span>Go Center</span>
                        <span>Suplementos</span>
                    @else
                        <span>{{ $storeName }}</span>
                    @endif
                </span>
            </a>
            <nav class="hidden items-center gap-5 lg:flex">
                <a class="muted-link" href="{{ route('products.index') }}">Catalogo</a>
                @foreach($navCategories as $category)
                    <a class="muted-link" href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                @endforeach
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('orders.lookup') }}" class="hidden muted-link sm:inline-flex">Consultar pedido</a>
                <a href="{{ route('cart.index') }}" class="btn-secondary relative px-3" aria-label="Carrito">
                    <i data-lucide="shopping-cart" class="h-4 w-4"></i>
                    <span class="accent-pill absolute -right-2 -top-2 grid h-5 min-w-5 place-items-center rounded-full px-1 text-xs font-black">{{ $cartCount }}</span>
                </a>
                <button class="btn-secondary px-3 lg:hidden" x-on:click="open = ! open" aria-label="Menu">
                    <i data-lucide="menu" class="h-4 w-4"></i>
                </button>
            </div>
        </div>
        <div class="border-t border-zinc-800 bg-zinc-950 lg:hidden" x-show="open">
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

    <main class="pt-16">
        @yield('content')
    </main>

    <footer class="mt-20 border-t border-zinc-800 bg-zinc-950">
        <div class="container-page grid gap-8 py-10 md:grid-cols-4">
            <div>
                <div class="font-black uppercase tracking-normal text-white">{{ $storeName }}</div>
                <p class="mt-3 text-sm text-zinc-400">Suplementos deportivos, paquetes, proteinas y accesorios con pagos seguros y envios a todo Mexico.</p>
            </div>
            <div>
                <div class="text-sm font-bold text-white">Tienda</div>
                <div class="mt-3 grid gap-2">
                    <a class="muted-link" href="{{ route('products.index') }}">Productos</a>
                    <a class="muted-link" href="{{ route('orders.lookup') }}">Consulta de pedido</a>
                    <a class="muted-link" href="{{ route('cart.index') }}">Carrito</a>
                </div>
            </div>
            <div>
                <div class="text-sm font-bold text-white">Politicas</div>
                <div class="mt-3 grid gap-2">
                    <a class="muted-link" href="{{ route('policies.privacy') }}">Privacidad</a>
                    <a class="muted-link" href="{{ route('policies.terms') }}">Terminos</a>
                    <a class="muted-link" href="{{ route('policies.returns') }}">Devoluciones</a>
                    <a class="muted-link" href="{{ route('policies.shipping') }}">Envios</a>
                </div>
            </div>
            <div>
                <div class="text-sm font-bold text-white">Admin</div>
                <div class="mt-3 grid gap-2">
                    <a class="muted-link" href="{{ route('admin.login') }}">Panel privado</a>
                </div>
            </div>
        </div>
    </footer>

    @if($whatsapp && ! request()->routeIs('checkout.show'))
        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" class="whatsapp-float fixed bottom-24 right-5 z-40 md:bottom-5" aria-label="WhatsApp">
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
            @if($cartCount > 0)
                <span class="accent-pill absolute right-3 top-1 grid h-4 min-w-4 place-items-center rounded-full px-1 text-[10px] font-black">{{ $cartCount }}</span>
            @endif
        </a>
    </nav>
</div>
</body>
</html>
