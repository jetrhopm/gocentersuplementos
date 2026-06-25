<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pedido {{ $order->folio }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #ddd; padding: 8px; text-align: left; }
        .total { font-size: 20px; font-weight: bold; }
    </style>
</head>
<body onload="window.print()">
    <h1>Pedido {{ $order->folio }}</h1>
    <p>{{ $order->customer_name }} · {{ $order->customer_phone }} · {{ $order->customer_email }}</p>
    <p>{{ $order->street }} {{ $order->external_number }} {{ $order->internal_number }}, {{ $order->neighborhood }}, {{ $order->city }}, {{ $order->state }}, {{ $order->postal_code }}</p>
    <table>
        <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Total</th></tr></thead>
        <tbody>
            @foreach($order->items as $item)
                <tr><td>{{ $item->product_name }} {{ $item->variant_name }}</td><td>{{ $item->quantity }}</td><td>${{ number_format($item->unit_price, 2) }}</td><td>${{ number_format($item->total, 2) }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <p class="total">Total: ${{ number_format($order->total, 2) }}</p>
</body>
</html>
