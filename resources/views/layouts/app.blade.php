<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sillarri Climb' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/climb.css">
</head>
<body>
    <div class="bg-layer"></div>

    <header class="site-header">
        <div class="header-topline">
            <a class="brand" href="{{ route('home') }}">Sillarri Climb</a>
            <div class="header-live-weather" aria-live="polite">
                <a id="headerWeatherTicker" class="header-live-text" href="{{ route('home') }}#home-weather">Eguraldia kargatzen...</a>
            </div>
        </div>

        <nav class="nav-links">
            <a class="kilter-nav" href="{{ route('kilter') }}">KILTER</a>
            <a href="{{ route('ranking') }}">Sailkapena</a>
            @auth
                <details class="user-menu">
                    <summary class="user-chip">
                        @php
                            $avatarPath = auth()->user()->avatar_path ?? '';
                            $avatarUrl = $avatarPath !== ''
                                ? (\Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])
                                    ? $avatarPath
                                    : '/storage/'.$avatarPath)
                                : '/images/default-avatar.svg';
                        @endphp
                        <img src="{{ $avatarUrl }}" alt="Lehenetsitako profileko irudia">
                        <span>{{ auth()->user()->username ?? auth()->user()->name }}</span>
                        @if((bool) auth()->user()->is_admin)
                            <span class="admin-pill admin-pill-icon" title="Administratzailea" aria-label="Administratzailea">🛡️</span>
                        @endif
                    </summary>
                    <div class="user-dropdown">
                        @if((bool) auth()->user()->is_admin)
                            <div class="admin-row">Kontu mota: Administratzailea</div>
                        @endif
                        @if(\Illuminate\Support\Facades\Route::has('users.public'))
                            <a href="{{ route('users.public', auth()->user()) }}">Estatistikak</a>
                        @endif
                        <a href="{{ route('settings') }}">Ezarpenak</a>
                        <hr>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Saioa itxi</button>
                        </form>
                    </div>
                </details>
            @else
                <a
                    href="{{ route('login') }}"
                    class="user-chip login-nav-chip"
                    aria-label="Sartu"
                    title="Sartu"
                >
                    <img src="/images/default-avatar.svg" alt="Sartu">
                </a>
            @endauth
        </nav>
    </header>

    <main>
        @if(session('status'))
            <div class="flash-ok">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>

    <script>
        (function () {
            const menus = document.querySelectorAll('.user-menu');

            if (!menus.length) return;

            document.addEventListener('click', (event) => {
                menus.forEach((menu) => {
                    if (!menu.open) return;
                    if (menu.contains(event.target)) return;
                    menu.open = false;
                });
            });
        })();

        (function () {
            const ticker = document.getElementById('headerWeatherTicker');
            if (!ticker) return;
            const weatherBaseUrl = "{{ route('weather') }}";

            const locations = [
                { name: 'Ereño', lat: 43.357, lon: -2.625 },
                { name: 'Urkiola', lat: 43.103, lon: -2.646 },
                { name: 'Mañaria', lat: 43.137, lon: -2.661 },
                { name: 'Atauri', lat: 42.736, lon: -2.455 },
                { name: 'Gernika', lat: 43.317, lon: -2.678 },
                { name: 'Turtzioz', lat: 43.272, lon: -3.255 },
                { name: 'Ramales', lat: 43.257, lon: -3.465 },
            ];

            function formatDateKey(date) {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }

            function targetDates() {
                const now = new Date();
                return [
                    { label: 'Gaur', key: formatDateKey(now) },
                ];
            }

            function iconFromWmo(code) {
                if ([0].includes(code)) return '☀️';
                if ([1, 2].includes(code)) return '⛅';
                if ([3].includes(code)) return '☁️';
                if ([45, 48].includes(code)) return '🌫️';
                if ([51, 53, 55, 56, 57].includes(code)) return '🌦️';
                if ([61, 63, 65, 66, 67, 80, 81, 82].includes(code)) return '🌧️';
                if ([71, 73, 75, 77, 85, 86].includes(code)) return '❄️';
                if ([95, 96, 99].includes(code)) return '⛈️';
                return '🌤️';
            }

            async function buildEntries() {
                const dates = targetDates();
                const entries = [];

                for (const location of locations) {
                    const apiUrl = new URL('https://api.open-meteo.com/v1/forecast');
                    apiUrl.searchParams.set('latitude', String(location.lat));
                    apiUrl.searchParams.set('longitude', String(location.lon));
                    apiUrl.searchParams.set('daily', 'weathercode,temperature_2m_max,temperature_2m_min');
                    apiUrl.searchParams.set('forecast_days', '10');
                    apiUrl.searchParams.set('timezone', 'Europe/Madrid');

                    const response = await fetch(apiUrl.toString(), { method: 'GET' });
                    if (!response.ok) {
                        continue;
                    }

                    const json = await response.json();
                    const daily = json.daily || {};
                    const times = Array.isArray(daily.time) ? daily.time : [];
                    const codes = Array.isArray(daily.weathercode) ? daily.weathercode : [];
                    const maxes = Array.isArray(daily.temperature_2m_max) ? daily.temperature_2m_max : [];
                    const mins = Array.isArray(daily.temperature_2m_min) ? daily.temperature_2m_min : [];

                    const byDate = new Map();
                    for (let i = 0; i < times.length; i++) {
                        byDate.set(times[i], {
                            code: Number(codes[i] ?? 0),
                            max: Number(maxes[i] ?? 0),
                            min: Number(mins[i] ?? 0),
                        });
                    }

                    const today = dates[0];
                    const item = byDate.get(today.key);
                    if (!item) continue;
                    entries.push({
                        text: `${location.name} · ${iconFromWmo(item.code)} ${Math.round(item.max)}°/${Math.round(item.min)}°`,
                        href: `${weatherBaseUrl}?place=${encodeURIComponent(location.name)}&date=${today.key}`,
                    });
                }

                return entries;
            }

            function showWithFade(entry) {
                ticker.classList.remove('is-visible');
                window.setTimeout(() => {
                    ticker.textContent = entry.text;
                    ticker.href = entry.href;
                    ticker.classList.add('is-visible');
                }, 220);
            }

            buildEntries()
                .then((entries) => {
                    if (!entries.length) {
                        ticker.textContent = 'Eguraldi daturik ez une honetan';
                        ticker.classList.add('is-visible');
                        return;
                    }

                    let index = 0;
                    showWithFade(entries[index]);
                    window.setInterval(() => {
                        index = (index + 1) % entries.length;
                        showWithFade(entries[index]);
                    }, 6000);
                })
                .catch(() => {
                    ticker.textContent = 'Eguraldi daturik ez une honetan';
                    ticker.classList.add('is-visible');
                });
        })();
    </script>
</body>
</html>
