<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="utf-8">
    <title>Sillarri · Eskaria bertan behera</title>
</head>
<body style="margin:0;background:#100e0d;color:#2a2722;font-family:'Trebuchet MS', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#100e0d;padding:28px 0;">
        <tr>
            <td align="center">
                <table width="620" cellpadding="0" cellspacing="0" style="background:#f6f1e8;border-radius:16px;overflow:hidden;border:1px solid #e7dccd;">
                    <tr>
                        <td style="padding:20px 24px;background:#1a1714;color:#f6f1e8;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="64" valign="middle">
                                        <img src="{{ asset('images/logo.png') }}" alt="Sillarri Climb" style="width:52px;height:52px;object-fit:contain;border-radius:12px;background:#11100e;padding:6px;">
                                    </td>
                                    <td valign="middle">
                                        <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#d97b6a;">Sillarri Climb</div>
                                        <div style="font-size:20px;font-weight:700;margin-top:4px;">Eskaria bertan behera</div>
                                        <div style="font-size:13px;color:#e8ddce;margin-top:2px;">Zure eskaria ezeztatu egin da.</div>
                                    </td>
                                    <td align="right" valign="middle">
                                        <span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#d97b6a;color:#1a1714;font-size:11px;font-weight:700;letter-spacing:1px;">EZEZTATUA</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:22px 24px;">
                            <p style="margin:0 0 12px;font-size:14px;color:#5e574d;">Eskaria #{{ $order->id }} · {{ $order->created_at?->format('Y-m-d H:i') }}</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;border-collapse:collapse;">
                                @foreach($items as $item)
                                    <tr>
                                        <td style="padding:8px 0;border-bottom:1px solid #e9dfd1;">
                                            <strong>{{ $item['name'] }}</strong>
                                            <div style="color:#6c655b;font-size:12px;margin-top:2px;">Kolorea: {{ $item['color'] }} · Talla: {{ $item['size'] }} · Kopurua: {{ $item['qty'] }}</div>
                                        </td>
                                        <td align="right" style="padding:8px 0;border-bottom:1px solid #e9dfd1;">{{ number_format($item['line_total'], 0) }} €</td>
                                    </tr>
                                @endforeach
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;">
                                <tr>
                                    <td style="font-weight:700;font-size:15px;">Guztira</td>
                                    <td align="right" style="font-weight:700;font-size:15px;">{{ number_format($total, 0) }} €</td>
                                </tr>
                            </table>
                            <p style="margin-top:16px;font-size:13px;color:#6c655b;">Zalantzarik baduzu, jarri gurekin harremanetan.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;background:#efe5d7;font-size:12px;color:#6c655b;">
                            Sillarri Climb · BELAIDXE denda
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
