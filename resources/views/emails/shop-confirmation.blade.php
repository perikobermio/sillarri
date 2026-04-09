<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="utf-8">
    <title>Sillarri · Erosketa baieztapena</title>
</head>
<body style="margin:0;background:#12110f;color:#2a2722;font-family:Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#12110f;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#f6f1e8;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:18px 24px;background:#1e1b18;color:#f6f1e8;">
                            <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80" alt="Sillarri" style="width:100%;max-height:160px;object-fit:cover;border-radius:8px;">
                            <h1 style="margin:14px 0 4px;font-size:20px;">Eskerrik asko, {{ $user->name ?? $user->username ?? 'Eskalatzailea' }}.</h1>
                            <p style="margin:0;font-size:14px;color:#e8ddce;">Zure erosketaren baieztapena jaso dugu.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px;">
                            <h2 style="margin:0 0 10px;font-size:16px;">Erosketa laburpena</h2>
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
                            <p style="margin-top:14px;font-size:13px;color:#6c655b;">
                                Laster zurekin harremanetan jarriko gara eta produktuak prest daudenean abisatuko zaitugu.
                            </p>
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
