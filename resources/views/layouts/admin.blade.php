<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('assets/gocenter/logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/gocenter/logo.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-950">
<div class="theme-{{ config('services.store.theme', 'volt') }} min-h-dvh w-full overflow-x-hidden bg-zinc-950" x-data="{ adminMenuOpen: false }">
    <header
        class="admin-header sticky top-0 z-40 border-b border-zinc-800 bg-zinc-950/95 backdrop-blur"
        x-on:click.outside="adminMenuOpen = false"
        x-on:keydown.escape.window="adminMenuOpen = false"
    >
        <div class="container-page flex h-16 items-center justify-between gap-4">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <span class="admin-brand-banner">
                    <img src="{{ asset('assets/gocenter/header-banner.jpg') }}" alt="{{ config('app.name') }}">
                </span>
                <span class="admin-brand-copy">
                    <span>Panel admin</span>
                    <small>{{ config('app.name') }}</small>
                </span>
            </a>
            <nav class="hidden items-center gap-2 md:flex">
                <a class="admin-nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="admin-nav-link" href="{{ route('admin.products.index') }}">Productos</a>
                <a class="admin-nav-link" href="{{ route('admin.categories.index') }}">Categorias</a>
                <a class="admin-nav-link" href="{{ route('admin.coupons.index') }}">Cupones</a>
                <a class="admin-nav-link" href="{{ route('admin.orders.index') }}">Pedidos</a>
                @if(auth()->user()?->isSuperAdmin())
                    <a class="admin-nav-link" href="{{ route('admin.users.index') }}">Administradores</a>
                @endif
                <a class="admin-nav-link" href="{{ route('admin.settings.index') }}">Configuracion</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="hidden btn-secondary px-3 sm:inline-flex" title="Ver tienda">
                    <i data-lucide="store" class="h-4 w-4"></i>
                    Tienda
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" class="hidden sm:block">
                    @csrf
                    <button class="btn-secondary px-3">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                        Salir
                    </button>
                </form>
                <button type="button" class="btn-secondary px-3 md:hidden" x-on:click="adminMenuOpen = ! adminMenuOpen" :aria-expanded="adminMenuOpen.toString()" aria-controls="admin-mobile-menu" aria-label="Menu admin">
                    <i data-lucide="menu" class="h-4 w-4" x-show="!adminMenuOpen"></i>
                    <i data-lucide="x" class="h-4 w-4" x-show="adminMenuOpen"></i>
                </button>
            </div>
        </div>
        <div id="admin-mobile-menu" class="admin-mobile-menu border-t border-zinc-800 bg-zinc-950 md:hidden" x-show="adminMenuOpen" x-transition>
            <nav class="container-page grid gap-2 py-4">
                <a class="admin-nav-link flex items-center gap-3" href="{{ route('admin.dashboard') }}">
                    <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                    Dashboard
                </a>
                <a class="admin-nav-link flex items-center gap-3" href="{{ route('admin.products.index') }}">
                    <i data-lucide="package" class="h-4 w-4"></i>
                    Productos
                </a>
                <a class="admin-nav-link flex items-center gap-3" href="{{ route('admin.categories.index') }}">
                    <i data-lucide="tags" class="h-4 w-4"></i>
                    Categorias
                </a>
                <a class="admin-nav-link flex items-center gap-3" href="{{ route('admin.coupons.index') }}">
                    <i data-lucide="ticket-percent" class="h-4 w-4"></i>
                    Cupones
                </a>
                <a class="admin-nav-link flex items-center gap-3" href="{{ route('admin.orders.index') }}">
                    <i data-lucide="clipboard-list" class="h-4 w-4"></i>
                    Pedidos
                </a>
                @if(auth()->user()?->isSuperAdmin())
                    <a class="admin-nav-link flex items-center gap-3" href="{{ route('admin.users.index') }}">
                        <i data-lucide="users-round" class="h-4 w-4"></i>
                        Administradores
                    </a>
                @endif
                <a class="admin-nav-link flex items-center gap-3" href="{{ route('admin.settings.index') }}">
                    <i data-lucide="settings" class="h-4 w-4"></i>
                    Configuracion
                </a>
                <a class="admin-nav-link flex items-center gap-3" href="{{ route('home') }}">
                    <i data-lucide="store" class="h-4 w-4"></i>
                    Ver tienda
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="admin-nav-link flex w-full items-center gap-3 text-left">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                        Salir
                    </button>
                </form>
            </nav>
        </div>
    </header>
    @if (session('status') || $errors->any())
        <div class="container-page pt-6">
            @if (session('status'))
                <div class="panel notice-success p-4 text-sm">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="panel border-red-500/40 bg-red-950/40 p-4 text-sm text-red-100">{{ $errors->first() }}</div>
            @endif
        </div>
    @endif
    <main class="container-page py-8">
        @yield('content')
    </main>
</div>
</body>
</html>
