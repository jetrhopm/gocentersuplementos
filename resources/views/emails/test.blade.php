<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Prueba de correo</title>
</head>
<body style="margin:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#18181b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e4e4e7;">
                    <tr>
                        <td style="background:#09090b;color:#ffffff;padding:24px;">
                            <div style="font-size:12px;text-transform:uppercase;letter-spacing:.12em;color:#ef4444;font-weight:bold;">GO Center Suplementos</div>
                            <h1 style="margin:10px 0 0;font-size:24px;line-height:1.2;">Correo configurado correctamente</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;font-size:15px;line-height:1.7;color:#3f3f46;">
                            <p style="margin:0;">Esta es una prueba enviada desde el panel administrador.</p>
                            <p style="margin:16px 0 0;">Remitente configurado: <strong>{{ $fromName }}</strong> &lt;{{ $fromAddress }}&gt;</p>
                            <p style="margin:16px 0 0;color:#71717a;font-size:13px;">Si recibiste este mensaje, Laravel ya puede enviar correos usando la configuracion actual.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
