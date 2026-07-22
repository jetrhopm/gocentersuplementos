<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recibo {{ $order->folio }}</title>
</head>
<body style="margin:0;background:#09090b;color:#f4f4f5;font-family:Arial,sans-serif;">
    <div style="max-width:640px;margin:0 auto;padding:28px 18px;">
        <div style="border:1px solid #27272a;background:#111113;border-radius:12px;padding:24px;">
            <h1 style="margin:0 0 8px;font-size:26px;text-transform:uppercase;">{{ $paymentReceived ? 'Pago recibido' : 'Recibo de pedido' }}</h1>
            <p style="margin:0;color:#a1a1aa;">
                {{ $paymentReceived ? 'Tu pago fue confirmado correctamente. Ya estamos preparando el siguiente paso de tu pedido.' : 'Gracias por comprar en '.config('app.name').'.' }}
            </p>

            <div style="margin-top:22px;padding:16px;border:1px solid #3f3f46;border-radius:10px;background:#09090b;">
                <p style="margin:0 0 8px;color:#a1a1aa;">Folio</p>
                <strong style="font-size:20px;color:#ffffff;">{{ $order->folio }}</strong>
                <p style="margin:14px 0 0;color:#a1a1aa;">Estado: <strong style="color:#ffffff;">{{ $order->statusLabel() }}</strong></p>
                <p style="margin:8px 0 0;color:#a1a1aa;">Total: <strong style="color:#ef4444;">${{ number_format((float) $order->total, 2) }} MXN</strong></p>
            </div>

            <h2 style="margin:24px 0 12px;font-size:16px;text-transform:uppercase;">Productos</h2>
            @foreach($order->items as $item)
                <div style="border-bottom:1px solid #27272a;padding:10px 0;">
                    <strong>{{ $item->product_name }}</strong>
                    <div style="color:#a1a1aa;font-size:14px;">{{ $item->quantity }} x ${{ number_format((float) $item->unit_price, 2) }}</div>
                </div>
            @endforeach

            @if($order->payment_method === 'oxxo')
                @php
                    $oxxoQrPath = trim((string) ($oxxo['qr_path'] ?? ''), '/');
                    $oxxoQrUrl = $oxxoQrPath !== '' ? url($oxxoQrPath) : null;
                    $oxxoReference = ($oxxo['reference'] ?? null) ?: $order->transferNumericReference();
                @endphp
                <div style="margin-top:24px;padding:18px;border:1px solid #3f3f46;border-radius:10px;background:#09090b;">
                    <h2 style="margin:0 0 10px;font-size:16px;text-transform:uppercase;color:#ffffff;">Pago en OXXO</h2>
                    <p style="margin:0 0 14px;color:#a1a1aa;line-height:1.5;">Muestra este codigo en OXXO para realizar tu pago. Conserva tu comprobante.</p>
                    @if($oxxoQrUrl)
                        <div style="background:#ffffff;border-radius:10px;padding:12px;text-align:center;">
                            <img src="{{ $oxxoQrUrl }}" alt="Codigo QR para pago en OXXO" style="max-width:280px;width:100%;height:auto;border-radius:8px;">
                        </div>
                    @endif
                    <p style="margin:16px 0 6px;color:#a1a1aa;">Si el cajero la solicita, tambien puedes dictar esta referencia:</p>
                    <div style="background:#18181b;border:1px solid #27272a;border-radius:8px;padding:14px;text-align:center;font-size:22px;font-weight:800;color:#ffffff;">
                        {{ $oxxoReference }}
                    </div>
                    <p style="margin:14px 0 0;color:#a1a1aa;line-height:1.5;">{{ $oxxo['instructions'] ?? '' }}</p>
                </div>
            @endif

            <a href="{{ $orderUrl }}" style="display:block;margin-top:24px;text-align:center;background:#e30613;color:#ffffff;text-decoration:none;border-radius:8px;padding:14px 18px;font-weight:800;text-transform:uppercase;">
                Ver mi pedido
            </a>
            <p style="margin:18px 0 0;color:#71717a;font-size:12px;line-height:1.5;">Este enlace abre tu pedido directamente, sin capturar folio ni correo.</p>
        </div>
    </div>
</body>
</html>
