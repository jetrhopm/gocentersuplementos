@extends('layouts.admin')

@section('title', 'Limpieza de catalogo')

@section('content')
<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <span class="badge">Superadmin</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">Eliminar productos por categoria</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-400">
            Usa esta herramienta solo despues de respaldar. Borra productos, variantes e imagenes de base de datos de una sola categoria sin tocar las demas.
        </p>
    </div>
    <a href="{{ route('admin.backups.catalog.index') }}" class="btn-secondary">
        <i data-lucide="database-backup" class="h-4 w-4"></i>
        Crear backup
    </a>
</div>

@error('cleanup')
    <div class="mt-5 rounded-md border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">{{ $message }}</div>
@enderror

<form method="GET" action="{{ route('admin.catalog-cleanup.index') }}" class="panel mt-6 grid gap-4 p-5 md:grid-cols-[1fr_auto]">
    <div class="field">
        <label for="category_id">Categoria</label>
        <select id="category_id" name="category_id" required>
            <option value="">Selecciona una categoria</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected($selectedCategory?->id === $category->id)>
                    {{ $category->name }} - {{ $category->products_count }} productos
                </option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end">
        <button class="btn-secondary min-h-11 w-full md:w-auto">
            <i data-lucide="eye" class="h-4 w-4"></i>
            Vista previa
        </button>
    </div>
</form>

@if($preview)
    <div class="mt-8 grid gap-4 md:grid-cols-5">
        <div class="admin-stat">
            <div class="relative z-10 flex items-center justify-between gap-3">
                <div class="text-sm text-zinc-400">Productos</div>
                <i data-lucide="package-x" class="accent-text h-5 w-5"></i>
            </div>
            <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $preview['product_count'] }}</div>
        </div>
        <div class="admin-stat">
            <div class="relative z-10 flex items-center justify-between gap-3">
                <div class="text-sm text-zinc-400">Variantes</div>
                <i data-lucide="layers-3" class="accent-text h-5 w-5"></i>
            </div>
            <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $preview['variant_count'] }}</div>
        </div>
        <div class="admin-stat">
            <div class="relative z-10 flex items-center justify-between gap-3">
                <div class="text-sm text-zinc-400">Imagenes BD</div>
                <i data-lucide="images" class="accent-text h-5 w-5"></i>
            </div>
            <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $preview['image_count'] }}</div>
        </div>
        <div class="admin-stat">
            <div class="relative z-10 flex items-center justify-between gap-3">
                <div class="text-sm text-zinc-400">Archivos borrables</div>
                <i data-lucide="file-x-2" class="accent-text h-5 w-5"></i>
            </div>
            <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $preview['deletable_file_count'] }}</div>
        </div>
        <div class="admin-stat">
            <div class="relative z-10 flex items-center justify-between gap-3">
                <div class="text-sm text-zinc-400">Compartidos</div>
                <i data-lucide="shield-check" class="accent-text h-5 w-5"></i>
            </div>
            <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $preview['shared_file_count'] }}</div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_0.72fr]">
        <section class="panel overflow-hidden">
            <div class="border-b border-zinc-800 p-5">
                <h2 class="text-xl font-black uppercase text-white">Vista previa</h2>
                <p class="mt-2 text-sm text-zinc-500">
                    Categoria: <span class="font-bold text-white">{{ $selectedCategory->name }}</span>
                    <span class="ml-2 rounded-full border border-zinc-700 px-2 py-0.5 text-[11px] uppercase text-zinc-400">{{ $selectedCategory->slug }}</span>
                </p>
            </div>

            <div class="max-h-[42rem] divide-y divide-zinc-800 overflow-y-auto">
                @forelse($preview['products'] as $product)
                    <article class="grid gap-4 p-4 sm:grid-cols-[5rem_1fr_auto] sm:items-center">
                        <div class="grid grid-cols-3 gap-1 sm:grid-cols-1">
                            @forelse($product->images->take(3) as $image)
                                <img src="{{ $image->url() }}" alt="{{ $image->alt ?: $product->name }}" class="aspect-square w-full rounded-md border border-zinc-800 object-cover">
                            @empty
                                <div class="grid aspect-square place-items-center rounded-md border border-zinc-800 bg-zinc-950 text-zinc-600">
                                    <i data-lucide="image-off" class="h-5 w-5"></i>
                                </div>
                            @endforelse
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-black text-white">{{ $product->name }}</h3>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-zinc-500">
                                <span>${{ number_format($product->price, 2) }}</span>
                                <span>{{ $product->stock }} stock</span>
                                <span>{{ $product->images->count() }} imagenes</span>
                                <span>{{ $product->variants->count() }} variantes</span>
                            </div>
                        </div>
                        <span class="badge w-fit">{{ $product->active ? 'Activo' : 'Inactivo' }}</span>
                    </article>
                @empty
                    <div class="p-5 text-sm text-zinc-400">Esta categoria no tiene productos.</div>
                @endforelse
            </div>
        </section>

        <aside class="space-y-6">
            <section class="panel p-5">
                <h2 class="text-xl font-black uppercase text-white">Archivos fisicos</h2>
                <p class="mt-3 text-sm leading-6 text-zinc-500">
                    Si activas el borrado fisico, solo se eliminan archivos locales que no esten compartidos por productos de otra categoria.
                </p>
                <div class="mt-4 max-h-64 space-y-2 overflow-y-auto pr-1">
                    @forelse($preview['images'] as $image)
                        <div class="interactive-tile flex items-center gap-3 p-2">
                            <img src="{{ $image['url'] }}" alt="" class="h-12 w-12 rounded-md border border-zinc-800 object-cover">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-xs font-bold text-zinc-300">{{ $image['path'] }}</div>
                                <div class="mt-1 flex flex-wrap gap-1 text-[11px]">
                                    @if(! $image['local'])
                                        <span class="rounded-full bg-zinc-800 px-2 py-0.5 text-zinc-400">externa</span>
                                    @elseif($image['shared'])
                                        <span class="rounded-full bg-amber-400/10 px-2 py-0.5 text-amber-200">compartida</span>
                                    @elseif(! $image['exists'])
                                        <span class="rounded-full bg-zinc-800 px-2 py-0.5 text-zinc-400">no existe</span>
                                    @else
                                        <span class="rounded-full bg-red-500/10 px-2 py-0.5 text-red-100">borrable</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">No hay imagenes registradas.</p>
                    @endforelse
                </div>
            </section>

            <form
                method="POST"
                action="{{ route('admin.catalog-cleanup.destroy') }}"
                class="panel border-red-500/30 bg-red-950/20 p-5"
                onsubmit="return confirm(@js('Esta accion borrara productos, variantes e imagenes de base de datos solo de la categoria '.$selectedCategory->name.'. ¿Continuar?'))"
            >
                @csrf
                @method('DELETE')
                <input type="hidden" name="category_id" value="{{ $selectedCategory->id }}">

                <h2 class="text-xl font-black uppercase text-red-100">Zona de borrado</h2>
                <p class="mt-3 text-sm leading-6 text-red-100/80">
                    Para confirmar escribe exactamente:
                    <span class="mt-2 block rounded-md border border-red-500/30 bg-red-950/50 px-3 py-2 font-mono text-red-50">{{ $selectedCategory->slug }}</span>
                </p>

                <div class="field mt-4">
                    <label for="confirmation">Confirmacion</label>
                    <input id="confirmation" name="confirmation" value="{{ old('confirmation') }}" placeholder="{{ $selectedCategory->slug }}" autocomplete="off">
                    @error('confirmation')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <label class="mt-4 flex items-start gap-3 rounded-md border border-zinc-800 bg-zinc-950/60 p-3 text-sm text-zinc-300">
                    <input type="checkbox" name="delete_files" value="1" class="mt-1 h-5 w-5 rounded border-zinc-700 bg-zinc-950 text-red-500 focus:ring-red-500">
                    <span>
                        Borrar tambien archivos fisicos seguros
                        <small class="mt-1 block text-zinc-500">Se conservan imagenes externas, inexistentes o usadas por otra categoria.</small>
                    </span>
                </label>

                <button class="btn-danger mt-5 min-h-12 w-full" @disabled($preview['product_count'] === 0)>
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                    Eliminar productos de esta categoria
                </button>
            </form>
        </aside>
    </div>
@else
    <div class="panel mt-6 p-5 text-sm leading-6 text-zinc-400">
        Selecciona una categoria para revisar exactamente que productos, variantes, imagenes y archivos podrian borrarse.
    </div>
@endif
@endsection
