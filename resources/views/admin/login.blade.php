@extends('layouts.app')

@section('title', 'Login admin')

@section('content')
<section class="container-page grid min-h-[70vh] place-items-center py-12">
    <form method="POST" action="{{ route('admin.authenticate') }}" class="panel w-full max-w-md p-8" x-data="{ showPassword: false }">
        @csrf
        <span class="badge">Panel privado</span>
        <h1 class="mt-4 text-3xl font-black uppercase text-white">Administrador</h1>
        <div class="mt-6 grid gap-4">
            <div class="field">
                <label>Correo</label>
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            </div>
            <div class="field">
                <label>Contraseña</label>
                <div class="relative">
                    <input
                        x-bind:type="showPassword ? 'text' : 'password'"
                        name="password"
                        autocomplete="current-password"
                        class="pr-12"
                        required
                    >
                    <button
                        type="button"
                        class="absolute inset-y-0 right-3 inline-flex items-center text-zinc-400 hover:text-white"
                        x-on:click="showPassword = ! showPassword"
                        x-bind:aria-label="showPassword ? 'Ocultar contraseña' : 'Ver contraseña'"
                    >
                        <i data-lucide="eye" class="h-5 w-5" x-show="!showPassword"></i>
                        <i data-lucide="eye-off" class="h-5 w-5" x-show="showPassword"></i>
                    </button>
                </div>
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
