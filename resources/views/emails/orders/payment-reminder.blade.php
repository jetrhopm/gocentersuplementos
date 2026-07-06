<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pago pendiente {{ $order->folio }}</title>
</head>
<body style="margin:0;background:#09090b;color:#f4f4f5;font-family:Arial,sans-serif;">
    <div style="max-width:640px;margin:0 auto;padding:28px 18px;">
        <div style="border:1px solid #27272a;background:#111113;border-radius:12px;padding:24px;">
            <h1 style="margin:0 0 8px;font-size:26px;text-transform:uppercase;">Tu pedido esta pendiente de pago</h1>
            <p style="margin:0;color:#a1a1aa;">Hola {{ $order->customer_name }}, tu pedido en {{ config('app.name') }} sigue reservado. Puedes completar tu pago en linea de forma segura.</p>

            <div style="margin-top:22px;padding:16px;border:1px solid #3f3f46;border-radius:10px;background:#09090b;">
                <p style="margin:0 0 8px;color:#a1a1aa;">Folio</p>
                <strong style="font-size:20px;color:#ffffff;">{{ $order->folio }}</strong>
                <p style="margin:14px 0 0;color:#a1a1aa;">Estado: <strong style="color:#ffffff;">{{ $order->statusLabel() }}</strong></p>
                <p style="margin:8px 0 0;color:#a1a1aa;">Total a pagar: <strong style="color:#ef4444;">${{ number_format((float) $order->total, 2) }} MXN</strong></p>
            </div>

            <h2 style="margin:24px 0 12px;font-size:16px;text-transform:uppercase;">Productos</h2>
            @foreach($order->items as $item)
                <div style="border-bottom:1px solid #27272a;padding:10px 0;">
                    <strong>{{ $item->product_name }}</strong>
                    <div style="color:#a1a1aa;font-size:14px;">{{ $item->quantity }} x ${{ number_format((float) $item->unit_price, 2) }}</div>
                </div>
            @endforeach

            <a href="{{ $orderUrl }}" style="display:block;margin-top:24px;text-align:center;background:#e30613;color:#ffffff;text-decoration:none;border-radius:8px;padding:14px 18px;font-weight:800;text-transform:uppercase;">
                Ver mi pedido y pagar
            </a>
            <p style="margin:18px 0 0;color:#71717a;font-size:12px;line-height:1.5;">Este enlace abre tu pedido con toda la informacion y el boton de pago, sin capturar folio ni correo. Si ya realizaste el pago, ignora este mensaje.</p>
        </div>
    </div>
</body>
</html>
