@extends('layouts.admin')

@section('title', $coupon->exists ? 'Editar cupon' : 'Crear cupon')

@section('content')
<div class="flex items-end justify-between gap-4">
    <div>
        <span class="badge">Cupon</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">{{ $coupon->exists ? 'Editar cupon' : 'Crear cupon' }}</h1>
    </div>
    <a href="{{ route('admin.coupons.index') }}" class="btn-secondary">Volver</a>
</div>

<form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="panel mt-6 grid gap-4 p-5 md:grid-cols-2">
    @csrf
    @if($coupon->exists) @method('PUT') @endif

    <div class="field">
        <label>Codigo</label>
        <input name="code" value="{{ old('code', $coupon->code) }}" placeholder="BIENVENIDA10" required>
    </div>
    <div class="field">
        <label>Tipo</label>
        <select name="type" required>
            <option value="percent" @selected(old('type', $coupon->type ?? 'percent') === 'percent')>Porcentaje</option>
            <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Monto fijo</option>
            <option value="free_shipping" @selected(old('type', $coupon->type) === 'free_shipping')>Envio gratis</option>
        </select>
    </div>
    <div class="field">
        <label>Valor</label>
        <input type="number" step="0.01" min="0.01" name="value" value="{{ old('value', $coupon->value) }}">
        <span class="text-xs text-zinc-500">Para envio gratis se guardara 100%, pero el descuento aplicado sera el costo real del envio.</span>
    </div>
    <div class="field">
        <label>Compra minima</label>
        <input type="number" step="0.01" min="0" name="minimum_total" value="{{ old('minimum_total', $coupon->minimum_total ?? 0) }}">
    </div>
    <div class="field">
        <label>Limite de usos</label>
        <input type="number" min="1" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" placeholder="Sin limite">
    </div>
    <div class="field">
        <label>Usos actuales</label>
        <input type="number" min="0" name="uses" value="{{ old('uses', $coupon->uses ?? 0) }}">
    </div>
    <div class="field">
        <label>Inicia</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="field">
        <label>Vence</label>
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <label class="md:col-span-2 flex items-center gap-2 rounded-md border border-zinc-800 bg-zinc-950 p-4">
        <input type="checkbox" name="active" value="1" @checked(old('active', $coupon->active ?? true))>
        <span class="font-bold text-white">Cupon activo</span>
    </label>

    <div class="md:col-span-2 flex justify-end">
        <button class="btn-primary min-h-12">
            <i data-lucide="save" class="h-4 w-4"></i>
            Guardar cupon
        </button>
    </div>
</form>
@endsection
