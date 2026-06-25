@extends('layouts.admin')

@section('title', $product->exists ? 'Editar producto' : 'Crear producto')

@section('content')
@php
    $variants = old('variants', $product->exists ? $product->variants->map(fn($v) => $v->only(['id','sku','size','color','flavor','presentation','price_modifier','stock','active']))->values()->all() : [[]]);
@endphp
<div class="flex items-center justify-between gap-4">
    <div>
        <span class="badge">Producto</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">{{ $product->exists ? 'Editar' : 'Crear' }}</h1>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn-secondary">Volver</a>
</div>

<form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="mt-8 grid gap-6">
    @csrf
    @if($product->exists) @method('PUT') @endif
    <div class="panel p-5">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="field"><label>Nombre</label><input name="name" value="{{ old('name', $product->name) }}" required></div>
            <div class="field"><label>Slug</label><input name="slug" value="{{ old('slug', $product->slug) }}"></div>
            <div class="field"><label>Categoria</label><select name="category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="field"><label>Marca</label><input name="brand" value="{{ old('brand', $product->brand) }}"></div>
            <div class="field"><label>SKU</label><input name="sku" value="{{ old('sku', $product->sku) }}"></div>
            <div class="field"><label>Stock</label><input type="number" name="stock" min="0" value="{{ old('stock', $product->stock ?? 0) }}"></div>
            <div class="field"><label>Precio</label><input name="price" inputmode="decimal" value="{{ old('price', $product->price) }}" required></div>
            <div class="field"><label>Precio antes</label><input name="compare_at_price" inputmode="decimal" value="{{ old('compare_at_price', $product->compare_at_price) }}"></div>
            <div class="field md:col-span-2"><label>Descripcion</label><textarea name="description" rows="5" required>{{ old('description', $product->description) }}</textarea></div>
            <div class="field"><label>Meta title</label><input name="meta_title" value="{{ old('meta_title', $product->meta_title) }}"></div>
            <div class="field"><label>Meta description</label><input name="meta_description" value="{{ old('meta_description', $product->meta_description) }}"></div>
            <label class="flex items-center gap-2"><input type="checkbox" name="featured" value="1" @checked(old('featured', $product->featured))> Destacado</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="active" value="1" @checked(old('active', $product->active ?? true))> Activo</label>
        </div>
    </div>

    <div class="panel p-5">
        <h2 class="text-xl font-black uppercase text-white">Imagenes</h2>
        <input class="mt-4 w-full" type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp">
        @if($product->exists && $product->images->isNotEmpty())
            <div class="mt-5 grid gap-4 sm:grid-cols-3 lg:grid-cols-5">
                @foreach($product->images as $image)
                    <div class="rounded-md border border-zinc-800 p-2">
                        <img src="{{ $image->url() }}" alt="{{ $image->alt }}" class="aspect-square rounded object-cover">
                        <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image->id]) }}" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button class="btn-danger w-full px-2 py-1">Eliminar</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="panel p-5" x-data="{ rows: @js($variants) }">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black uppercase text-white">Variantes</h2>
            <button type="button" class="btn-secondary" x-on:click="rows.push({active: true})">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Agregar
            </button>
        </div>
        <template x-for="(row, index) in rows" :key="index">
            <div class="mt-4 grid gap-3 rounded-md border border-zinc-800 bg-zinc-950 p-4 md:grid-cols-4">
                <input type="hidden" :name="`variants[${index}][id]`" x-model="row.id">
                <div class="field"><label>SKU</label><input :name="`variants[${index}][sku]`" x-model="row.sku"></div>
                <div class="field"><label>Talla</label><input :name="`variants[${index}][size]`" x-model="row.size"></div>
                <div class="field"><label>Color</label><input :name="`variants[${index}][color]`" x-model="row.color"></div>
                <div class="field"><label>Sabor</label><input :name="`variants[${index}][flavor]`" x-model="row.flavor"></div>
                <div class="field"><label>Presentacion</label><input :name="`variants[${index}][presentation]`" x-model="row.presentation"></div>
                <div class="field"><label>Modificador</label><input :name="`variants[${index}][price_modifier]`" x-model="row.price_modifier"></div>
                <div class="field"><label>Stock</label><input type="number" min="0" :name="`variants[${index}][stock]`" x-model="row.stock"></div>
                <label class="mt-7 flex items-center gap-2"><input type="checkbox" value="1" :name="`variants[${index}][active]`" x-model="row.active"> Activa</label>
            </div>
        </template>
    </div>

    <button class="btn-primary w-fit min-w-48">
        <i data-lucide="save" class="h-4 w-4"></i>
        Guardar
    </button>
</form>
@endsection
