<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pago recibido {{ $order->folio }}</title>
</head>
<body style="margin:0;background:#09090b;color:#f4f4f5;font-family:Arial,sans-serif;">
    <div style="max-width:680px;margin:0 auto;padding:28px 18px;">
        <div style="border:1px solid #27272a;background:#111113;border-radius:12px;padding:24px;">
            <p style="margin:0 0 8px;color:#22c55e;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;">Pago aprobado</p>
            <h1 style="margin:0;font-size:26px;text-transform:uppercase;">{{ $order->folio }}</h1>
            <p style="margin:10px 0 0;color:#a1a1aa;">El pago del pedido fue confirmado y el inventario fue actualizado.</p>

            <div style="margin-top:22px;padding:16px;border:1px solid #3f3f46;border-radius:10px;background:#09090b;">
                <p style="margin:0 0 8px;color:#a1a1aa;">Cliente</p>
                <strong style="color:#ffffff;">{{ $order->customer_name }}</strong>
                <p style="margin:8px 0 0;color:#a1a1aa;">{{ $order->customer_email }} · {{ $order->customer_phone }}</p>
            </div>

            <div style="margin-top:16px;padding:16px;border:1px solid #3f3f46;border-radius:10px;background:#09090b;">
                <p style="margin:0 0 8px;color:#a1a1aa;">Pago</p>
                <p style="margin:0;color:#ffffff;">Metodo: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
                <p style="margin:8px 0 0;color:#ffffff;">Total: <strong style="color:#22c55e;">${{ number_format((float) $order->total, 2) }} MXN</strong></p>
                @if($order->payment?->receipt_no)
                    <p style="margin:8px 0 0;color:#a1a1aa;">Recibo: {{ $order->payment->receipt_no }}</p>
                @endif
                @if($order->payment?->transaction_id)
                    <p style="margin:8px 0 0;color:#a1a1aa;">Transaccion: {{ $order->payment->transaction_id }}</p>
                @endif
            </div>

            <h2 style="margin:24px 0 12px;font-size:16px;text-transform:uppercase;">Productos</h2>
            @foreach($order->items as $item)
                <div style="border-bottom:1px solid #27272a;padding:10px 0;">
                    <strong>{{ $item->product_name }}</strong>
                    <div style="color:#a1a1aa;font-size:14px;">{{ $item->quantity }} x ${{ number_format((float) $item->unit_price, 2) }} = ${{ number_format((float) $item->total, 2) }}</div>
                </div>
            @endforeach

            <a href="{{ $adminOrderUrl }}" style="display:block;margin-top:24px;text-align:center;background:#22c55e;color:#07110b;text-decoration:none;border-radius:8px;padding:14px 18px;font-weight:800;text-transform:uppercase;">
                Ver pedido en admin
            </a>
        </div>
    </div>
</body>
</html>
