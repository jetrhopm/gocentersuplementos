@extends('layouts.admin')

@section('title', 'Administradores')

@section('content')
<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <span class="badge">Accesos</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">Administradores</h1>
        <p class="mt-2 max-w-2xl text-sm text-zinc-500">Gestiona usuarios del panel. Solo un superadmin puede crear, editar o eliminar administradores.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">
        <i data-lucide="user-plus" class="h-4 w-4"></i>
        Crear admin
    </a>
</div>

<form method="GET" class="panel mt-6 flex flex-col gap-3 p-4 sm:flex-row">
    <input name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o correo" class="min-w-0 flex-1">
    <button class="btn-secondary">
        <i data-lucide="search" class="h-4 w-4"></i>
        Buscar
    </button>
</form>

<div class="panel mt-6 overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-xs uppercase text-zinc-500">
            <tr>
                <th class="p-4">Usuario</th>
                <th class="p-4">Rol</th>
                <th class="p-4">Estado</th>
                <th class="p-4">Creado</th>
                <th class="p-4"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-800">
            @forelse($users as $adminUser)
                <tr>
                    <td class="p-4">
                        <div class="font-black text-white">{{ $adminUser->name }}</div>
                        <div class="text-zinc-500">{{ $adminUser->email }}</div>
                    </td>
                    <td class="p-4">
                        <span class="badge">{{ $adminUser->role === \App\Models\User::ROLE_SUPER_ADMIN ? 'Superadmin' : 'Admin' }}</span>
                    </td>
                    <td class="p-4 text-zinc-300">{{ $adminUser->active ? 'Activo' : 'Inactivo' }}</td>
                    <td class="p-4 text-zinc-500">{{ $adminUser->created_at?->format('d/m/Y') }}</td>
                    <td class="p-4">
                        <div class="flex gap-2">
                            <a class="btn-secondary px-3" href="{{ route('admin.users.edit', $adminUser) }}" aria-label="Editar">
                                <i data-lucide="pencil" class="h-4 w-4"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.users.destroy', $adminUser) }}" onsubmit="return confirm('Eliminar administrador {{ $adminUser->email }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-danger px-3" aria-label="Eliminar" @disabled(auth()->id() === $adminUser->id)>
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-zinc-400">No hay administradores registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $users->links() }}</div>
@endsection
