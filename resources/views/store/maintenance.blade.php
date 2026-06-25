@extends('layouts.app')

@section('title', 'Mantenimiento | '.config('app.name'))

@section('content')
<section class="container-page grid min-h-[70vh] place-items-center py-16">
    <div class="panel max-w-2xl p-8 text-center">
        <span class="badge">Mantenimiento</span>
        <h1 class="mt-4 text-4xl font-black uppercase text-white">Volvemos en breve</h1>
        <p class="mt-4 text-zinc-300">La tienda esta recibiendo ajustes. El panel administrador sigue disponible.</p>
    </div>
</section>
@endsection
