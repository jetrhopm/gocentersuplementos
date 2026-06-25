@extends('layouts.admin')

@section('title', 'Categorias')

@section('content')
<div class="flex items-end justify-between gap-4">
    <div><span class="badge">Catalogo</span><h1 class="mt-3 text-3xl font-black uppercase text-white">Categorias</h1></div>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary"><i data-lucide="plus" class="h-4 w-4"></i>Crear</a>
</div>
<div class="panel mt-6 overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-xs uppercase text-zinc-500"><tr><th class="p-4">Nombre</th><th class="p-4">Slug</th><th class="p-4">Orden</th><th class="p-4">Estado</th><th class="p-4"></th></tr></thead>
        <tbody class="divide-y divide-zinc-800">
            @foreach($categories as $category)
                <tr>
                    <td class="p-4 font-bold text-white">{{ $category->name }}</td>
                    <td class="p-4 text-zinc-400">{{ $category->slug }}</td>
                    <td class="p-4 text-zinc-400">{{ $category->sort_order }}</td>
                    <td class="p-4"><span class="badge">{{ $category->active ? 'Activa' : 'Inactiva' }}</span></td>
                    <td class="p-4">
                        <div class="flex gap-2">
                            <a class="btn-secondary px-3" href="{{ route('admin.categories.edit', $category) }}"><i data-lucide="pencil" class="h-4 w-4"></i></a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">@csrf @method('DELETE')<button class="btn-danger px-3"><i data-lucide="ban" class="h-4 w-4"></i></button></form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $categories->links() }}</div>
@endsection
