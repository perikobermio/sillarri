<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="utf-8">
    <title>Sillarri · Eskaria ezeztatua</title>
</head>
<body style="margin:0;background:#12110f;color:#2a2722;font-family:Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#12110f;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="620" cellpadding="0" cellspacing="0" style="background:#f6f1e8;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:18px 24px;background:#1e1b18;color:#f6f1e8;">
                            <img src="https://images.unsplash.com/photo-1452626038306-9aae5e071dd3?auto=format&fit=crop&w=900&q=80" alt="Sillarri" style="width:100%;max-height:160px;object-fit:cover;border-radius:8px;">
                            <h1 style="margin:14px 0 4px;font-size:20px;">Eskaria ezeztatua</h1>
                            <p style="margin:0;font-size:14px;color:#e8ddce;">Eskaria #{{ $order->id }} ezeztatu egin da.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 24px;">
                            <h2 style="margin:0 0 10px;font-size:16px;">Bezeroaren datuak</h2>
                            <p style="margin:0 0 8px;font-size:14px;">
                                <strong>Izena:</strong> {{ $order->user?->name ?? $order->user?->username ?? '—' }}<br>
                                <strong>Erabiltzailea:</strong> {{ $order->user?->username ?? '—' }}<br>
                                <strong>Emaila:</strong> {{ $order->email }}
                            </p>

                            <h2 style="margin:14px 0 10px;font-size:16px;">Eskariaren xehetasunak</h2>
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                @foreach($items as $item)
                                    <tr>
                                        <td style="padding:6px 0;">
                                            <strong>{{ $item['name'] }}</strong>
                                            <div style="color:#6c655b;font-size:12px;">
                                                Kolorea: {{ $item['color'] }} · Talla: {{ $item['size'] }} · Kopurua: {{ $item['qty'] }}
                                            </div>
                                        </td>
                                        <td align="right" style="padding:6px 0;">{{ number_format($item['line_total'], 0) }} €</td>
                                    </tr>
                                @endforeach
                            </table>
                            <div style="margin-top:12px;font-weight:700;">Guztira: {{ number_format($total, 0) }} €</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;background:#f0e6d8;font-size:12px;color:#6c655b;">
                            Sillarri Climb · BELAIDXE denda
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
