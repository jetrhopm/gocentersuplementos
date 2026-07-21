@extends('layouts.admin')

@section('title', 'Backup de catalogo')

@section('content')
@php
    $categoryRows = collect($summary['category_rows']);
    $allCategoryIds = $categoryRows->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
    $selectedCategoryIds = collect(old('category_ids', $allCategoryIds))->map(fn ($id) => (int) $id)->values()->all();
@endphp

<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <span class="badge">Backup</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">Catalogo por categorias</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-400">
            Selecciona las categorias que quieres respaldar. El archivo descargado separa cada categoria en su propia carpeta con SQL, JSON, imagenes y banner cuando exista.
        </p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn-secondary">
        <i data-lucide="arrow-left" class="h-4 w-4"></i>
        Volver
    </a>
</div>

@error('backup')
    <div class="mt-5 rounded-md border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">{{ $message }}</div>
@enderror
@error('category_ids')
    <div class="mt-5 rounded-md border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">{{ $message }}</div>
@enderror

<div class="mt-8 grid gap-4 md:grid-cols-4">
    <div class="admin-stat">
        <div class="relative z-10 flex items-center justify-between gap-3">
            <div class="text-sm text-zinc-400">Categorias</div>
            <i data-lucide="tags" class="accent-text h-5 w-5"></i>
        </div>
        <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $summary['categories'] }}</div>
    </div>
    <div class="admin-stat">
        <div class="relative z-10 flex items-center justify-between gap-3">
            <div class="text-sm text-zinc-400">Banners categoria</div>
            <i data-lucide="image" class="accent-text h-5 w-5"></i>
        </div>
        <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $summary['category_banners'] }}</div>
    </div>
    <div class="admin-stat">
        <div class="relative z-10 flex items-center justify-between gap-3">
            <div class="text-sm text-zinc-400">Productos</div>
            <i data-lucide="package" class="accent-text h-5 w-5"></i>
        </div>
        <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $summary['products'] }}</div>
    </div>
    <div class="admin-stat">
        <div class="relative z-10 flex items-center justify-between gap-3">
            <div class="text-sm text-zinc-400">Imagenes producto</div>
            <i data-lucide="images" class="accent-text h-5 w-5"></i>
        </div>
        <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $summary['product_images'] }}</div>
    </div>
</div>

<form
    method="POST"
    action="{{ route('admin.backups.catalog.download') }}"
    class="mt-8 grid gap-6 lg:grid-cols-[1fr_0.72fr]"
    x-data="{ selected: @js($selectedCategoryIds), allIds: @js($allCategoryIds) }"
>
    @csrf

    <section class="panel p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black uppercase text-white">Categorias a respaldar</h2>
                <p class="mt-2 text-sm text-zinc-500">
                    Cada categoria marcada se guardara separada dentro del backup.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:flex">
                <button type="button" class="btn-secondary min-h-10 justify-center px-4 text-xs" x-on:click="selected = [...allIds]">
                    <i data-lucide="check-check" class="h-4 w-4"></i>
                    Marcar todas
                </button>
                <button type="button" class="btn-secondary min-h-10 justify-center px-4 text-xs" x-on:click="selected = []">
                    <i data-lucide="square" class="h-4 w-4"></i>
                    Desmarcar
                </button>
            </div>
        </div>

        <div class="mt-5 grid gap-3">
            @foreach($categoryRows as $category)
                <label class="interactive-tile flex cursor-pointer items-start gap-3 p-4 transition hover:border-red-500/40">
                    <input
                        type="checkbox"
                        name="category_ids[]"
                        value="{{ $category['id'] }}"
                        class="mt-1 h-5 w-5 rounded border-zinc-700 bg-zinc-950 text-red-500 focus:ring-red-500"
                        x-model.number="selected"
                    >
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-bold text-white">{{ $category['name'] }}</span>
                            <span class="rounded-full border border-zinc-700 px-2 py-0.5 text-[11px] uppercase tracking-wide text-zinc-400">{{ $category['slug'] }}</span>
                            @if($category['has_banner'])
                                <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-2 py-0.5 text-[11px] uppercase tracking-wide text-emerald-200">Banner</span>
                            @endif
                        </div>
                        <div class="mt-2 flex flex-wrap gap-3 text-xs text-zinc-500">
                            <span>{{ $category['products_count'] }} productos</span>
                            <span>{{ $category['product_images_count'] }} imagenes</span>
                            <span>SQL propio</span>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>
    </section>

    <aside class="panel h-fit p-5">
        <h2 class="text-xl font-black uppercase text-white">Generar backup</h2>
        <p class="mt-3 text-sm leading-6 text-zinc-500">
            El archivo se genera al momento y se elimina del servidor despues de enviarlo al navegador.
        </p>

        <div class="mt-5 rounded-md border border-zinc-800 bg-zinc-950/70 p-4">
            <div class="flex items-center justify-between gap-3">
                <span class="text-sm text-zinc-400">Seleccionadas</span>
                <span class="text-2xl font-black text-white" x-text="selected.length"></span>
            </div>
            <div class="mt-4 space-y-2 text-sm text-zinc-400">
                <div class="flex items-center gap-2">
                    <i data-lucide="folder-tree" class="accent-text h-4 w-4"></i>
                    Carpeta por categoria
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="database" class="accent-text h-4 w-4"></i>
                    Un SQL por categoria
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="image" class="accent-text h-4 w-4"></i>
                    Imagenes y banner local si existe
                </div>
            </div>
        </div>

        <button
            class="btn-primary mt-5 min-h-12 w-full"
            x-bind:disabled="selected.length === 0"
            x-bind:class="selected.length === 0 ? 'cursor-not-allowed opacity-50' : ''"
            onclick="return confirm('Se generara un backup separado por categorias. ¿Continuar?')"
        >
            <i data-lucide="download-cloud" class="h-4 w-4"></i>
            Descargar backup
        </button>

        <div class="mt-5 rounded-md border border-amber-400/30 bg-amber-400/10 p-4 text-sm leading-6 text-amber-100">
            No contiene credenciales, pero si contiene precios, inventario, imagenes y estructura del catalogo seleccionado.
        </div>
    </aside>
</form>
@endsection
