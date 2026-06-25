@extends('layouts.app')

@section('title', 'Login admin')

@section('content')
<section class="container-page grid min-h-[70vh] place-items-center py-12">
    <form method="POST" action="{{ route('admin.authenticate') }}" class="panel w-full max-w-md p-8">
        @csrf
        <span class="badge">Panel privado</span>
        <h1 class="mt-4 text-3xl font-black uppercase text-white">Administrador</h1>
        <div class="mt-6 grid gap-4">
            <div class="field">
                <label>Correo</label>
                <input type="email" name="email" value="{{ old('email', 'admin@local.test') }}" required autofocus>
            </div>
            <div class="field">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" value="1">
                Recordarme
            </label>
            <button class="btn-primary min-h-12">
                <i data-lucide="log-in" class="h-4 w-4"></i>
                Entrar
            </button>
        </div>
    </form>
</section>
@endsection
