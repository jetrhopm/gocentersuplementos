@extends('layouts.admin')

@section('title', 'Productos')

@section('content')
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

<form class="mt-6 flex flex-col gap-3 sm:flex-row">
    <input name="q" value="{{ request('q') }}" placeholder="Buscar producto" class="sm:w-80">
    <select name="status" class="sm:w-48">
        <option value="">Todos</option>
        <option value="active" @selected(request('status') === 'active')>Activos</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
    </select>
    <button class="btn-secondary">Filtrar</button>
</form>

<div class="panel mt-6 overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-xs uppercase text-zinc-500">
            <tr><th class="p-4">Producto</th><th class="p-4">Categoria</th><th class="p-4">Precio</th><th class="p-4">Stock</th><th class="p-4">Estado</th><th class="p-4"></th></tr>
        </thead>
        <tbody class="divide-y divide-zinc-800">
            @foreach($products as $product)
                <tr>
                    <td class="p-4 font-bold text-white">{{ $product->name }}</td>
                    <td class="p-4 text-zinc-400">{{ $product->category->name }}</td>
                    <td class="p-4 price-text">${{ number_format($product->price, 2) }}</td>
                    <td class="p-4 text-zinc-300">{{ $product->stock }}</td>
                    <td class="p-4"><span class="badge">{{ $product->active ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="p-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn-secondary px-3"><i data-lucide="pencil" class="h-4 w-4"></i></a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn-danger px-3"><i data-lucide="ban" class="h-4 w-4"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
