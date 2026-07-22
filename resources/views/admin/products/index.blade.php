@extends('layouts.admin')

@section('title', 'Productos')

@section('content')
@php
    $sort = request('sort');
    $direction = request('direction', 'desc');
    $sortLink = function (string $key) use ($sort, $direction) {
        $nextDirection = $sort === $key && $direction === 'asc' ? 'desc' : 'asc';

        return route('admin.products.index', array_merge(request()->except('page'), [
            'sort' => $key,
            'direction' => $nextDirection,
        ]));
    };
    $sortIcon = fn (string $key) => $sort === $key
        ? ($direction === 'asc' ? 'arrow-up-narrow-wide' : 'arrow-down-wide-narrow')
        : 'chevrons-up-down';
@endphp

<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <span class="badge">Inventario</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">Productos</h1>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn-primary">
        <i data-lucide="plus" class="h-4 w-4"></i>
        Crear producto
    </a>
</div>

<form class="mt-6 grid gap-3 xl:grid-cols-[minmax(0,1fr)_13rem_18rem_11rem_14rem]">
    <input name="q" value="{{ request('q') }}" placeholder="Buscar producto" class="min-w-0">
    <select name="category" class="min-w-0">
        <option value="">Todas las categorias</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <div class="grid grid-cols-2 gap-3">
        <input type="number" name="price_min" value="{{ request('price_min') }}" min="0" step="0.01" inputmode="decimal" placeholder="Precio min" class="min-w-0">
        <input type="number" name="price_max" value="{{ request('price_max') }}" min="0" step="0.01" inputmode="decimal" placeholder="Precio max" class="min-w-0">
    </div>
    <select name="status" class="min-w-0">
        <option value="">Todos</option>
        <option value="active" @selected(request('status') === 'active')>Activos</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
    </select>
    <div class="grid grid-cols-2 gap-3">
        <button class="btn-secondary justify-center">Filtrar</button>
        <a href="{{ route('admin.products.index') }}" class="btn-secondary justify-center">Limpiar</a>
    </div>
</form>

<div class="panel mt-6 overflow-x-auto">
    <table class="w-full min-w-[960px] text-left text-sm">
        <thead class="border-b border-zinc-800 text-xs uppercase text-zinc-500">
            <tr>
                <th class="p-4">Imagen</th>
                <th class="p-4">
                    <a href="{{ $sortLink('name') }}" class="inline-flex items-center gap-2 transition hover:text-white">
                        Producto
                        <i data-lucide="{{ $sortIcon('name') }}" class="h-3.5 w-3.5"></i>
                    </a>
                </th>
                <th class="p-4">
                    <a href="{{ $sortLink('category') }}" class="inline-flex items-center gap-2 transition hover:text-white">
                        Categoria
                        <i data-lucide="{{ $sortIcon('category') }}" class="h-3.5 w-3.5"></i>
                    </a>
                </th>
                <th class="p-4">
                    <a href="{{ $sortLink('price') }}" class="inline-flex items-center gap-2 transition hover:text-white">
                        Precio
                        <i data-lucide="{{ $sortIcon('price') }}" class="h-3.5 w-3.5"></i>
                    </a>
                </th>
                <th class="p-4">
                    <a href="{{ $sortLink('compare_at_price') }}" class="inline-flex items-center gap-2 transition hover:text-white">
                        Precio antes
                        <i data-lucide="{{ $sortIcon('compare_at_price') }}" class="h-3.5 w-3.5"></i>
                    </a>
                </th>
                <th class="p-4">Stock</th>
                <th class="p-4">Visibilidad</th>
                <th class="p-4">Destacado</th>
                <th class="p-4 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-800">
            @forelse($products as $product)
                @php($image = $product->images->first())
                <tr class="transition hover:bg-white/[0.03]">
                    <td class="p-4">
                        <a href="{{ route('admin.products.edit', $product) }}" class="group flex h-16 w-16 items-center justify-center overflow-hidden rounded-lg border border-zinc-800 bg-zinc-900 transition hover:border-red-500/70" title="Editar {{ $product->name }}">
                            @if($image)
                                <img src="{{ $image->url() }}" alt="{{ $image->alt ?: $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <i data-lucide="image-off" class="h-5 w-5 text-zinc-600"></i>
                            @endif
                        </a>
                    </td>
                    <td class="p-4">
                        <a href="{{ route('admin.products.edit', $product) }}" class="font-bold text-white transition hover:text-red-300">
                            {{ $product->name }}
                        </a>
                        @if($product->sku)
                            <div class="mt-1 text-xs text-zinc-500">SKU {{ $product->sku }}</div>
                        @endif
                    </td>
                    <td class="p-4 text-zinc-400">{{ $product->category?->name ?? 'Sin categoria' }}</td>
                    <td class="p-4 price-text">${{ number_format($product->price, 2) }}</td>
                    <td class="p-4">
                        @if($product->compare_at_price)
                            <span class="text-zinc-300">${{ number_format($product->compare_at_price, 2) }}</span>
                        @else
                            <span class="text-zinc-600">No aplica</span>
                        @endif
                    </td>
                    <td class="p-4 text-zinc-300">{{ $product->stock }}</td>
                    <td class="p-4">
                        <span class="badge">{{ $product->active ? 'Visible' : 'Oculto' }}</span>
                    </td>
                    <td class="p-4">
                        <span class="badge">{{ $product->featured ? 'Si' : 'No' }}</span>
                    </td>
                    <td class="p-4">
                        <div class="flex min-w-max justify-end gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn-secondary px-3" title="Editar producto" aria-label="Editar {{ $product->name }}">
                                <i data-lucide="pencil" class="h-4 w-4"></i>
                            </a>
                            <form
                                method="POST"
                                action="{{ route('admin.products.visibility', $product) }}"
                                onsubmit="return confirm(@js(($product->active ? 'Vas a ocultar el producto "'.$product->name.'" de la tienda. El cliente no podra verlo ni comprarlo. ¿Continuar?' : 'Vas a activar el producto "'.$product->name.'" para que pueda mostrarse en tienda si su categoria tambien esta activa. ¿Continuar?')))"
                            >
                                @csrf
                                @method('PATCH')
                                <button class="btn-secondary px-3" type="submit" title="{{ $product->active ? 'Ocultar en tienda' : 'Mostrar en tienda' }}" aria-label="{{ $product->active ? 'Ocultar' : 'Mostrar' }} {{ $product->name }}">
                                    <i data-lucide="{{ $product->active ? 'eye-off' : 'eye' }}" class="h-4 w-4"></i>
                                </button>
                            </form>
                            <form
                                method="POST"
                                action="{{ route('admin.products.featured', $product) }}"
                                onsubmit="return confirm(@js(($product->featured ? 'Vas a quitar "'.$product->name.'" de destacados. ¿Continuar?' : 'Vas a marcar "'.$product->name.'" como destacado en la tienda. ¿Continuar?')))"
                            >
                                @csrf
                                @method('PATCH')
                                <button class="btn-secondary px-3" type="submit" title="{{ $product->featured ? 'Quitar destacado' : 'Marcar destacado' }}" aria-label="{{ $product->featured ? 'Quitar destacado' : 'Marcar destacado' }} {{ $product->name }}">
                                    <i data-lucide="star" class="h-4 w-4 {{ $product->featured ? 'fill-yellow-300 text-yellow-300' : '' }}"></i>
                                </button>
                            </form>
                            <form
                                method="POST"
                                action="{{ route('admin.products.destroy', $product) }}"
                                onsubmit="return confirm(@js('Vas a aplicar borrado logico al producto "'.$product->name.'". No se elimina de la base de datos ni se borran sus imagenes, pero quedara oculto de la tienda y retirado de destacados. ¿Deseas continuar?'))"
                            >
                                @csrf
                                @method('DELETE')
                                <button class="btn-danger px-3" type="submit" title="Borrado logico" aria-label="Borrado logico {{ $product->name }}">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="p-8 text-center text-zinc-400">No hay productos con esos filtros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
