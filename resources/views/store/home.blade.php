@extends('layouts.app')

@section('title', config('app.name').' | Suplementos deportivos')
@section('meta_description', 'Go Center Suplementos: proteinas, creatinas, paquetes, accesorios y envios a todo Mexico desde Los Mochis y Guasave.')

@php
    $storeName = config('app.name', 'Go Center Suplementos');
    $goCenterBanners = collect([
        [
            'kicker' => 'Go Center Suplementos',
            'title' => 'Packs Inlabs listos para entrenar',
            'copy' => 'Proteinas, creatinas y paquetes promocionales con la imagen real de Go Center.',
            'meta' => 'GO CENTER SUPLEMENTOS',
            'cta' => 'Ver productos',
            'href' => route('products.index', ['category' => 'packs-gocenter']),
            'image' => asset('assets/gocenter/banner.jpg'),
        ],
    ])->merge($heroProducts->map(fn ($product) => [
        'kicker' => $product->category?->name ?? 'Go Center',
        'title' => $product->name,
        'copy' => str($product->description)->limit(112)->toString(),
        'meta' => '$'.number_format((float) $product->price, 2).' MXN',
        'cta' => 'Ver paquete',
        'href' => route('products.show', $product),
        'image' => $product->displayImage(),
    ]))->values();
    $categoryImages = [
        'proteinas' => 'https://images.unsplash.com/photo-1593095948071-474c5cc2989d?auto=format&fit=crop&w=900&q=80',
        'pre-entrenos' => 'https://images.unsplash.com/photo-1549476464-37392f717541?auto=format&fit=crop&w=900&q=80',
        'creatinas' => 'https://images.unsplash.com/photo-1622484212850-eb596d769edc?auto=format&fit=crop&w=900&q=80',
        'accesorios-deportivos' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=900&q=80',
        'packs-gocenter' => asset('assets/gocenter/products/pack-isolate-inlabs-3790.jpg'),
        'ropa-deportiva-hombre' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80',
        'ropa-deportiva-mujer' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=900&q=80',
        'ofertas' => 'https://images.unsplash.com/photo-1599058917212-d750089bc07e?auto=format&fit=crop&w=900&q=80',
    ];
@endphp

@section('content')
<section class="relative overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=2200&q=85" alt="Entrenamiento intenso en gimnasio" class="h-full w-full object-cover opacity-30">
        <div class="absolute inset-0 bg-[linear-gradient(90deg,#050505_0%,rgba(5,5,5,.94)_38%,rgba(5,5,5,.44)_100%)]"></div>
    </div>

    <div class="container-page relative grid min-h-[78vh] items-center gap-10 py-14 lg:grid-cols-[1.05fr_.95fr] lg:py-20">
        <div>
            <span class="badge">Los Mochis y Guasave</span>
            <h1 class="hero-title mt-5 font-black text-white">{{ $storeName }}</h1>
            <p class="hero-copy mt-6 max-w-2xl">Suplementos deportivos, proteinas, creatinas y paquetes listos para entrenar con disciplina. Compra en linea con envio a todo Mexico y atencion directa por WhatsApp.</p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('products.index') }}" class="btn-primary min-h-12">
                    <i data-lucide="shopping-bag" class="h-4 w-4"></i>
                    Comprar ahora
                </a>
                <a href="{{ route('products.index', ['category' => 'packs-gocenter']) }}" class="btn-secondary min-h-12">
                    <i data-lucide="badge-percent" class="h-4 w-4"></i>
                    Ver packs
                </a>
            </div>
        </div>

        <div class="store-carousel" data-carousel data-carousel-interval="6500">
            @foreach($goCenterBanners as $banner)
                <article
                    class="store-banner overflow-hidden @if($loop->first) active @endif"
                    data-carousel-slide
                    aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                >
                    <img src="{{ $banner['image'] }}" alt="{{ $banner['title'] }}" class="store-banner-image absolute inset-0 h-full w-full object-cover opacity-48">
                    <div class="absolute inset-0 bg-[linear-gradient(90deg,#050505_0%,rgba(5,5,5,.92)_42%,rgba(5,5,5,.24)_100%)]"></div>
                    <div class="relative grid min-h-[27rem] items-end gap-6 p-5 pb-20 sm:p-8 sm:pb-20 lg:min-h-[31rem] lg:p-10 lg:pb-20">
                        <div class="max-w-3xl">
                            <span class="badge">{{ $banner['kicker'] }}</span>
                            <h2 class="store-banner-title mt-5 font-black text-white">{{ $banner['title'] }}</h2>
                            <p class="mt-5 max-w-2xl text-base leading-7 text-zinc-300 sm:text-lg">{{ $banner['copy'] }}</p>
                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center gap-2 text-sm font-bold uppercase text-zinc-300">
                                    <i data-lucide="map-pin" class="accent-text h-4 w-4"></i>
                                    {{ $banner['meta'] }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ $banner['href'] }}" class="btn-primary min-h-12 justify-self-start">
                            {{ $banner['cta'] }}
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </a>
                    </div>
                </article>
            @endforeach

            <div class="absolute left-4 top-1/2 hidden -translate-y-1/2 gap-2 sm:flex">
                <button type="button" class="store-banner-control" data-carousel-prev aria-label="Banner anterior">
                    <i data-lucide="chevron-left" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="absolute right-4 top-1/2 hidden -translate-y-1/2 gap-2 sm:flex">
                <button type="button" class="store-banner-control" data-carousel-next aria-label="Banner siguiente">
                    <i data-lucide="chevron-right" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="store-banner-pagination">
                @foreach($goCenterBanners as $banner)
                    <button type="button" class="store-banner-dot @if($loop->first) active @endif" data-carousel-dot="{{ $loop->index }}" aria-label="Ver banner {{ $loop->iteration }}"></button>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="metric-strip">
    <div class="container-page grid gap-4 py-5 sm:grid-cols-3">
        <div class="flex items-center gap-3">
            <i data-lucide="zap" class="accent-text h-5 w-5"></i>
            <div><div class="font-black text-white">Paquetes listos</div><div class="text-sm text-zinc-500">Combos para cada objetivo</div></div>
        </div>
        <div class="flex items-center gap-3">
            <i data-lucide="shield-check" class="accent-text h-5 w-5"></i>
            <div><div class="font-black text-white">Pago seguro</div><div class="text-sm text-zinc-500">Procesado con estandares de seguridad</div></div>
        </div>
        <div class="flex items-center gap-3">
            <i data-lucide="truck" class="accent-text h-5 w-5"></i>
            <div><div class="font-black text-white">Envios a Mexico</div><div class="text-sm text-zinc-500">Gratis desde $999 MXN</div></div>
        </div>
    </div>
</section>

@if($carouselProducts->isNotEmpty())
    <section class="container-page py-14">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <span class="badge">Paquetes agregados</span>
                <h2 class="section-heading mt-3">Carrusel Go Center</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-400">Productos seleccionados desde configuracion para destacar promociones, combos y banners recientes.</p>
            </div>
            <div class="flex gap-2">
                <button type="button" class="store-banner-control" data-scroll-carousel-prev="home-products" aria-label="Producto anterior">
                    <i data-lucide="chevron-left" class="h-5 w-5"></i>
                </button>
                <button type="button" class="store-banner-control" data-scroll-carousel-next="home-products" aria-label="Producto siguiente">
                    <i data-lucide="chevron-right" class="h-5 w-5"></i>
                </button>
            </div>
        </div>

        <div class="product-carousel mt-8" id="home-products" data-scroll-carousel>
            @foreach($carouselProducts as $product)
                <a href="{{ route('products.show', $product) }}" class="product-carousel-card">
                    <span class="product-carousel-image">
                        <img src="{{ $product->displayImage() }}" alt="{{ $product->name }}">
                    </span>
                    <span class="block p-4">
                        <span class="badge">{{ $product->category?->name ?? 'Producto' }}</span>
                        <span class="product-title mt-3 block min-h-12 text-sm font-black uppercase leading-tight text-white">{{ $product->name }}</span>
                        <span class="mt-3 flex items-end justify-between gap-3">
                            <span class="price-text text-xl font-black">${{ number_format((float) $product->price, 2) }}</span>
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-zinc-800 text-zinc-100">
                                <i data-lucide="arrow-right" class="h-4 w-4"></i>
                            </span>
                        </span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endif

<section class="container-page py-14">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <span class="badge">Explora</span>
            <h2 class="section-heading mt-3">Categorias</h2>
        </div>
        <a href="{{ route('products.index') }}" class="btn-secondary">
            Todo el catalogo
            <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category) }}" class="category-card min-h-56">
                <img src="{{ $categoryImages[$category->slug] ?? $categoryImages['proteinas'] }}" alt="{{ $category->name }}" class="absolute inset-0 h-full w-full object-cover opacity-72">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/35 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-5">
                    <div class="text-xs uppercase text-zinc-300">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="mt-1 text-xl font-black uppercase text-white">{{ $category->name }}</div>
                </div>
            </a>
        @endforeach
    </div>
</section>

<section class="quiet-band py-14">
    <div class="container-page">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <span class="badge">Destacados</span>
                <h2 class="section-heading mt-3">Recomendados Go Center</h2>
            </div>
            <form action="{{ route('products.index') }}" class="flex flex-col gap-2 sm:flex-row">
                <input name="q" placeholder="Buscar whey, creatina, leggings..." class="min-h-11 min-w-0 sm:w-80">
                <button class="btn-primary min-h-11">
                    <i data-lucide="search" class="h-4 w-4"></i>
                    Buscar
                </button>
            </form>
        </div>

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($featured as $product)
                @include('partials.product-card', ['product' => $product])
            @empty
                <div class="panel p-6 text-zinc-400">Aun no hay productos destacados.</div>
            @endforelse
        </div>
    </div>
</section>

@if($offers->isNotEmpty())
    <section class="container-page py-14">
        <div class="grid gap-6 lg:grid-cols-[.85fr_1.15fr] lg:items-center">
            <div>
                <span class="badge">Promos</span>
                <h2 class="section-heading mt-3">Paquetes para entrenar serio</h2>
                <p class="mt-4 max-w-xl leading-7 text-zinc-400">Combos y productos con descuento activo, pensados para completar tu rutina con proteina, creatina y accesorios.</p>
                <a href="{{ route('offers') }}" class="btn-primary mt-6">
                    Ver todas
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach($offers as $product)
                    <a href="{{ route('products.show', $product) }}" class="product-card grid grid-cols-[7rem_1fr] overflow-hidden">
                        <div class="product-image-wrap">
                            <img src="{{ $product->displayImage() }}" alt="{{ $product->name }}" class="h-full min-h-36 w-full object-cover">
                        </div>
                        <div class="p-4">
                            <span class="badge">Oferta</span>
                            <div class="mt-3 font-black uppercase text-white">{{ $product->name }}</div>
                            <div class="mt-3 flex items-end gap-2">
                                <span class="price-text text-xl font-black">${{ number_format($product->price, 2) }}</span>
                                <span class="text-sm text-zinc-500 line-through">${{ number_format($product->compare_at_price, 2) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
