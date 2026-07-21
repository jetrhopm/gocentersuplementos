@extends('layouts.app')

@section('title', 'Catalogo | '.config('app.name'))
@section('meta_description', 'Catalogo filtrable de suplementos, proteinas, creatinas, accesorios y paquetes deportivos.')

@if(! empty($metaSearchEvent))
    @push('scripts')
        <script>
            window.goMetaTrack?.(
                @json($metaSearchEvent['name']),
                @json($metaSearchEvent['custom_data']),
                @json($metaSearchEvent['event_id'])
            );
        </script>
    @endpush
@endif

@section('content')
@php
    $activeFilters = collect([
        request('q') ? 'Busqueda: '.request('q') : null,
        request('brand') ? 'Marca: '.request('brand') : null,
        request('min_price') ? 'Desde $'.request('min_price') : null,
        request('max_price') ? 'Hasta $'.request('max_price') : null,
        request('size') ? 'Talla: '.request('size') : null,
        request()->boolean('available') ? 'Disponibles' : null,
    ])->filter();
@endphp

<section class="quiet-band">
    <div class="container-page py-10">
        <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-end">
            <div>
                <span class="badge">Catalogo</span>
                <h1 class="section-heading mt-3">{{ $currentCategory?->name ?? 'Productos' }}</h1>
                <p class="mt-3 max-w-2xl text-zinc-400">Encuentra rapido paquetes, suplementos y ropa deportiva para tu siguiente entrenamiento.</p>
            </div>
            <button type="button" class="search-trigger min-h-12" data-search-open>
                <i data-lucide="search" class="h-5 w-5"></i>
                <span class="min-w-0 flex-1 text-left">
                    <span class="block text-xs font-bold uppercase text-zinc-500">Buscar</span>
                    <span class="block truncate text-sm text-white">{{ request('q') ?: 'Producto, marca o paquete' }}</span>
                </span>
                <i data-lucide="sliders-horizontal" class="h-4 w-4 text-zinc-500"></i>
            </button>
        </div>

        @if($activeFilters->isNotEmpty())
            <div class="mt-5 flex flex-wrap gap-2">
                @foreach($activeFilters as $filter)
                    <span class="filter-chip">{{ $filter }}</span>
                @endforeach
                <a href="{{ route('products.index') }}" class="filter-chip clear">Limpiar</a>
            </div>
        @endif

        <dialog id="product-search-dialog" class="search-dialog">
            <div class="search-modal-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="badge">Buscar</span>
                        <h2 class="mt-3 text-2xl font-black uppercase text-white">Encuentra tu producto</h2>
                    </div>
                    <button type="button" class="btn-secondary px-3" aria-label="Cerrar" data-search-close>
                        <i data-lucide="x" class="h-4 w-4"></i>
                    </button>
                </div>

                <form action="{{ route('products.index') }}" class="mt-6 grid gap-5">
                    <div class="input-shell">
                        <i data-lucide="search" class="input-icon"></i>
                        <input data-search-input name="q" value="{{ request('q') }}" placeholder="Busca por producto, marca o paquete" class="text-base">
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($categories as $category)
                            <label class="category-filter-card">
                                <input type="radio" name="category" value="{{ $category->slug }}" class="sr-only" @checked(request('category') === $category->slug || ($currentCategory?->slug === $category->slug))>
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <details class="filter-details" @open($activeFilters->isNotEmpty())>
                        <summary class="filter-toggle">
                            <span><i data-lucide="sliders-horizontal" class="h-4 w-4"></i> Afinar busqueda</span>
                            <i data-lucide="chevron-down" class="filter-chevron h-4 w-4 transition"></i>
                        </summary>

                        <div class="advanced-filter-grid mt-4">
                            <div class="field">
                                <label>Marca</label>
                                <select name="brand">
                                    <option value="">Todas</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand }}" @selected(request('brand') === $brand)>{{ $brand }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Talla</label>
                                <select name="size">
                                    <option value="">Todas</option>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size }}" @selected(request('size') === $size)>{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Precio desde</label>
                                <input name="min_price" value="{{ request('min_price') }}" inputmode="decimal" placeholder="$0">
                            </div>
                            <div class="field">
                                <label>Precio hasta</label>
                                <input name="max_price" value="{{ request('max_price') }}" inputmode="decimal" placeholder="$9999">
                            </div>
                            <div class="field">
                                <label>Orden</label>
                                <select name="sort">
                                    <option value="">Destacados</option>
                                    <option value="newest" @selected(request('sort') === 'newest')>Mas nuevos</option>
                                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Precio menor</option>
                                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Precio mayor</option>
                                </select>
                            </div>
                            <label class="availability-toggle">
                                <input type="checkbox" name="available" value="1" @checked(request()->boolean('available'))>
                                <span><i data-lucide="package-check" class="h-4 w-4"></i> Solo disponibles</span>
                            </label>
                        </div>
                    </details>

                    <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
                        <button class="btn-primary min-h-12">
                            <i data-lucide="search" class="h-4 w-4"></i>
                            Ver productos
                        </button>
                        <a href="{{ route('products.index') }}" class="btn-secondary min-h-12">Limpiar filtros</a>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
</section>

<section class="container-page py-10">
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category) }}" class="rounded-full border px-3 py-1.5 text-sm font-bold transition {{ $currentCategory?->slug === $category->slug ? 'border-transparent' : 'border-zinc-800 text-zinc-400 hover:text-white' }}" @if($currentCategory?->slug === $category->slug) style="background: var(--accent); color: var(--accent-contrast);" @endif>
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($products as $product)
                @include('partials.product-card', ['product' => $product])
            @empty
                <div class="panel p-8 text-zinc-400 sm:col-span-2 xl:col-span-3">No encontramos productos con esos filtros.</div>
            @endforelse
        </div>
        <div class="mt-8">{{ $products->links() }}</div>
    </div>
</section>
@endsection
