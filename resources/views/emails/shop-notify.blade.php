<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="utf-8">
    <title>Sillarri · Eskari berria</title>
</head>
<body style="margin:0;background:#0f1114;color:#1b1f23;font-family:Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0f1114;padding:28px 0;">
        <tr>
            <td align="center">
                <table width="660" cellpadding="0" cellspacing="0" style="background:#f4f6f7;border-radius:14px;overflow:hidden;border:1px solid #dfe6ea;">
                    <tr>
                        <td style="padding:18px 24px;background:#14191c;color:#f4f6f7;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="60" valign="middle">
                                        <img src="{{ asset('images/sillarri_belaidxe.png') }}" alt="Sillarri BELAIDXE" style="width:48px;height:48px;object-fit:contain;border-radius:10px;background:#0f1114;padding:6px;">
                                    </td>
                                    <td valign="middle">
                                        <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#86b8a1;">Sillarri Merch</div>
                                        <div style="font-size:20px;font-weight:700;margin-top:4px;">Eskari berria jaso da</div>
                                        <div style="font-size:12px;color:#c8d0d6;margin-top:2px;">Eskaria #{{ $order->id }} · {{ $order->created_at?->format('Y-m-d H:i') }}</div>
                                    </td>
                                    <td align="right" valign="middle">
                                        <span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#86b8a1;color:#0f1114;font-size:11px;font-weight:700;letter-spacing:1px;">ESKARI BERRIA</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;background:#eef2f4;border-radius:10px;">
                                <tr>
                                    <td style="padding:12px 14px;">
                                        <strong>Bezeroaren datuak</strong><br>
                                        Izena: {{ $order->user?->name ?? $order->user?->username ?? '—' }}<br>
                                        Erabiltzailea: {{ $order->user?->username ?? '—' }}<br>
                                        Emaila: {{ $order->email }}
                                    </td>
                                </tr>
                            </table>

                            <h2 style="margin:16px 0 10px;font-size:15px;">Eskariaren xehetasunak</h2>
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;border-collapse:collapse;">
                                @foreach($items as $item)
                                    <tr>
                                        <td style="padding:8px 0;border-bottom:1px solid #e3e9ed;">
                                            <strong>{{ $item['name'] }}</strong>
                                            <div style="color:#5f6b75;font-size:12px;margin-top:2px;">Kolorea: {{ $item['color'] }} · Talla: {{ $item['size'] }} · Kopurua: {{ $item['qty'] }}</div>
                                        </td>
                                        <td align="right" style="padding:8px 0;border-bottom:1px solid #e3e9ed;">{{ number_format($item['line_total'], 0) }} €</td>
                                    </tr>
                                @endforeach
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;">
                                <tr>
                                    <td style="font-weight:700;">Guztira</td>
                                    <td align="right" style="font-weight:700;">{{ number_format($total, 0) }} €</td>
                                </tr>
                            </table>
                            <p style="margin-top:14px;font-size:12px;color:#5f6b75;">Mesedez, prestatu produktuak eta jarri bezeroarekin harremanetan.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 24px;background:#e7ecef;font-size:12px;color:#5f6b75;">
                            Sillarri Climb · BELAIDXE denda
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
