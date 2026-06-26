@extends('layouts.admin')

@section('title', 'Cupones')

@section('content')
<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <span class="badge">Promociones</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">Cupones</h1>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="btn-primary">
        <i data-lucide="plus" class="h-4 w-4"></i>
        Crear cupon
    </a>
</div>

<form method="GET" class="panel mt-6 flex flex-col gap-3 p-4 sm:flex-row">
    <input name="q" value="{{ request('q') }}" placeholder="Buscar cupon" class="min-w-0 flex-1">
    <button class="btn-secondary">
        <i data-lucide="search" class="h-4 w-4"></i>
        Buscar
    </button>
</form>

<div class="panel mt-6 overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-xs uppercase text-zinc-500">
            <tr>
                <th class="p-4">Codigo</th>
                <th class="p-4">Descuento</th>
                <th class="p-4">Minimo</th>
                <th class="p-4">Usos</th>
                <th class="p-4">Vigencia</th>
                <th class="p-4">Estado</th>
                <th class="p-4"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-800">
            @forelse($coupons as $coupon)
                <tr>
                    <td class="p-4 font-black text-white">{{ $coupon->code }}</td>
                    <td class="p-4 text-zinc-300">
                        {{ $coupon->type === 'percent' ? number_format((float) $coupon->value, 0).'%': '$'.number_format((float) $coupon->value, 2) }}
                    </td>
                    <td class="p-4 text-zinc-400">${{ number_format((float) $coupon->minimum_total, 2) }}</td>
                    <td class="p-4 text-zinc-400">{{ $coupon->uses }} / {{ $coupon->max_uses ?? 'Sin limite' }}</td>
                    <td class="p-4 text-zinc-400">
                        {{ $coupon->starts_at?->format('d/m/Y') ?? 'Ahora' }} - {{ $coupon->expires_at?->format('d/m/Y') ?? 'Sin vencimiento' }}
                    </td>
                    <td class="p-4"><span class="badge">{{ $coupon->active ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="p-4">
                        <div class="flex gap-2">
                            <a class="btn-secondary px-3" href="{{ route('admin.coupons.edit', $coupon) }}" aria-label="Editar">
                                <i data-lucide="pencil" class="h-4 w-4"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Eliminar cupon {{ $coupon->code }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-danger px-3" aria-label="Eliminar">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-6 text-zinc-400">No hay cupones registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $coupons->links() }}</div>
@endsection
