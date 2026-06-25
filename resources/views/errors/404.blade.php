@extends('layouts.app')

@section('title', '404 | '.config('app.name'))

@section('content')
<section class="container-page grid min-h-[70vh] place-items-center py-16">
    <div class="panel max-w-2xl p-8 text-center">
        <span class="badge">404</span>
        <h1 class="mt-4 text-4xl font-black uppercase text-white">Pagina no encontrada</h1>
        <p class="mt-4 text-zinc-300">La ruta no existe o el producto ya no esta activo.</p>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('home') }}" class="btn-primary">
                <i data-lucide="home" class="h-4 w-4"></i>
                Inicio
            </a>
            <a href="{{ route('products.index') }}" class="btn-secondary">
                <i data-lucide="shopping-bag" class="h-4 w-4"></i>
                Catalogo
            </a>
        </div>
    </div>
</section>
@endsection
