@extends('layouts.admin')

@section('title', $adminUser->exists ? 'Editar administrador' : 'Crear administrador')

@section('content')
<div class="flex items-end justify-between gap-4">
    <div>
        <span class="badge">Acceso admin</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">{{ $adminUser->exists ? 'Editar administrador' : 'Crear administrador' }}</h1>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn-secondary">Volver</a>
</div>

<form method="POST" action="{{ $adminUser->exists ? route('admin.users.update', $adminUser) : route('admin.users.store') }}" class="panel mt-6 grid gap-4 p-5 md:grid-cols-2">
    @csrf
    @if($adminUser->exists) @method('PUT') @endif

    <div class="field">
        <label>Nombre</label>
        <input name="name" value="{{ old('name', $adminUser->name) }}" placeholder="Nombre del administrador" required>
    </div>
    <div class="field">
        <label>Correo</label>
        <input type="email" name="email" value="{{ old('email', $adminUser->email) }}" placeholder="admin@dominio.com" required>
    </div>
    <div class="field">
        <label>Rol</label>
        <select name="role" required>
            <option value="{{ \App\Models\User::ROLE_ADMIN }}" @selected(old('role', $adminUser->role) === \App\Models\User::ROLE_ADMIN)>Admin</option>
            <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}" @selected(old('role', $adminUser->role) === \App\Models\User::ROLE_SUPER_ADMIN)>Superadmin</option>
        </select>
    </div>
    <label class="flex items-center gap-2 rounded-md border border-zinc-800 bg-zinc-950 p-4">
        <input type="checkbox" name="active" value="1" @checked(old('active', $adminUser->active ?? true))>
        <span class="font-bold text-white">Usuario activo</span>
    </label>
    <div class="field">
        <label>{{ $adminUser->exists ? 'Nueva contrasena' : 'Contrasena' }}</label>
        <input type="password" name="password" autocomplete="new-password" minlength="8" @required(! $adminUser->exists)>
        @if($adminUser->exists)
            <p class="text-xs font-bold text-zinc-500">Dejala vacia para conservar la contrasena actual.</p>
        @endif
    </div>
    <div class="field">
        <label>Confirmar contrasena</label>
        <input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" @required(! $adminUser->exists)>
    </div>

    <div class="md:col-span-2 rounded-lg border border-amber-400/25 bg-amber-400/10 p-4 text-sm leading-6 text-amber-100">
        El admin normal puede gestionar productos, categorias, cupones, pedidos permitidos y configuracion basica. Solo el superadmin debe cambiar usuarios, APIs, banco y configuraciones sensibles.
    </div>

    <div class="md:col-span-2 flex justify-end">
        <button class="btn-primary min-h-12">
            <i data-lucide="save" class="h-4 w-4"></i>
            Guardar administrador
        </button>
    </div>
</form>
@endsection
