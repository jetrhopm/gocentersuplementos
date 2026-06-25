@extends('layouts.admin')

@section('title', $category->exists ? 'Editar categoria' : 'Crear categoria')

@section('content')
<div class="flex items-end justify-between gap-4">
    <div><span class="badge">Categoria</span><h1 class="mt-3 text-3xl font-black uppercase text-white">{{ $category->exists ? 'Editar' : 'Crear' }}</h1></div>
    <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Volver</a>
</div>
<form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="panel mt-6 grid gap-4 p-5">
    @csrf
    @if($category->exists) @method('PUT') @endif
    <div class="field"><label>Nombre</label><input name="name" value="{{ old('name', $category->name) }}" required></div>
    <div class="field"><label>Slug</label><input name="slug" value="{{ old('slug', $category->slug) }}"></div>
    <div class="field"><label>Descripcion</label><textarea name="description" rows="4">{{ old('description', $category->description) }}</textarea></div>
    <div class="field"><label>Orden</label><input type="number" name="sort_order" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}"></div>
    <label class="flex items-center gap-2"><input type="checkbox" name="active" value="1" @checked(old('active', $category->active ?? true))> Activa</label>
    <button class="btn-primary w-fit"><i data-lucide="save" class="h-4 w-4"></i>Guardar</button>
</form>
@endsection
