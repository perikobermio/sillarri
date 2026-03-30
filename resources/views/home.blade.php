@extends('layouts.app', ['title' => 'Sillarri Climb | Eskalada'])

@section('content')
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">Eskalada komunitatea</p>
        <h1>Igo gorago, entrenatu hobeto, partekatu bloke bakoitza.</h1>
        <p class="lead">
            Sillarri Climb-ek bideak, entrenamenduak eta sokakideak leku berean batzen ditu,
            haitza bizi duen jendearentzat.
        </p>

        <div class="cta-row">
            @auth
                <a class="btn btn-primary" href="{{ route('users.public', auth()->user()) }}">Nire profilera joan</a>
            @else
                <a class="btn btn-primary" href="{{ route('register') }}">Hasi doan</a>
                <a class="btn btn-secondary" href="{{ route('login') }}">Dagoeneko kontua dut</a>
            @endauth
        </div>
    </div>

    <div class="hero-card">
        <img src="https://images.unsplash.com/photo-1522163182402-834f871fd851?auto=format&fit=crop&w=1200&q=80" alt="Eskalatzailea harkaitz horman">
        <div class="card-overlay">
            <h2>Asteko bidea</h2>
            <p>"Aresta del Vent" · 6c · 38m · Kareharri teknikoa</p>
        </div>
    </div>
</section>

<a class="kilter-spotlight" href="{{ route('kilter') }}">
    <p class="eyebrow">Atal nagusia</p>
    <h2>KILTER</h2>
    <p>Plaka eta desplome saioak, ranking-a, entrenamendua eta asteko erronkak.</p>
    <span>KILTER atalera sartu</span>
</a>

<section id="home-weather" class="weather-home">
    <div class="weather-home-head">
        <p class="eyebrow">Aurrerapenaren jarraipena</p>
        <h3>Eskalada guneetako eguraldi aurreikuspena</h3>
    </div>
    <div class="weather-table-wrap">
        <table class="weather-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Gaur</th>
                    <th>Bihar</th>
                    <th>Larunbata</th>
                    <th>Igandea</th>
                </tr>
            </thead>
            <tbody id="weatherTableBody">
                <tr>
                    <td colspan="5" class="weather-loading-cell">Eguraldia kargatzen...</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<script>
    (function () {
        const locations = [
            { name: 'Ereño', lat: 43.357, lon: -2.625 },
            { name: 'Urkiola', lat: 43.103, lon: -2.646 },
            { name: 'Mañaria', lat: 43.137, lon: -2.661 },
            { name: 'Atauri', lat: 42.736, lon: -2.455 },
            { name: 'Gernika', lat: 43.317, lon: -2.678 },
            { name: 'Turtzioz', lat: 43.272, lon: -3.255 },
            { name: 'Ramales', lat: 43.257, lon: -3.465 },
        ];

        const weatherTableBody = document.getElementById('weatherTableBody');
        if (!weatherTableBody) return;

        function formatDateKey(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function nextWeekday(baseDate, weekday) {
            const next = new Date(baseDate);
            const distance = (weekday - baseDate.getDay() + 7) % 7;
            next.setDate(baseDate.getDate() + distance);
            return next;
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

        function targetDates() {
            const now = new Date();
            const today = new Date(now);
            const next = new Date(now);
            next.setDate(now.getDate() + 1);
            const saturday = nextWeekday(now, 6);
            const sunday = nextWeekday(now, 0);

            return [
                { key: 'today', date: today },
                { key: 'next', date: next },
                { key: 'saturday', date: saturday },
                { key: 'sunday', date: sunday },
            ];
        }

        function buildDayCell(daySpec, weatherByDate) {
            const data = weatherByDate.get(formatDateKey(daySpec.date));
            if (!data) {
                return '<span class="weather-pill weather-pill-empty">—</span>';
            }

            return `
                <span class="weather-pill">
                    <span class="weather-icon" aria-hidden="true">${iconFromWmo(data.code)}</span>
                    <span>${Math.round(data.max)}°/${Math.round(data.min)}°</span>
                </span>
            `;
        }

        async function fetchLocationForecast(location) {
            const apiUrl = new URL('https://api.open-meteo.com/v1/forecast');
            apiUrl.searchParams.set('latitude', String(location.lat));
            apiUrl.searchParams.set('longitude', String(location.lon));
            apiUrl.searchParams.set('daily', 'weathercode,temperature_2m_max,temperature_2m_min');
            apiUrl.searchParams.set('forecast_days', '10');
            apiUrl.searchParams.set('timezone', 'Europe/Madrid');

            const response = await fetch(apiUrl.toString(), { method: 'GET' });
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const json = await response.json();
            const daily = json.daily || {};
            const times = Array.isArray(daily.time) ? daily.time : [];
            const codes = Array.isArray(daily.weathercode) ? daily.weathercode : [];
            const maxes = Array.isArray(daily.temperature_2m_max) ? daily.temperature_2m_max : [];
            const mins = Array.isArray(daily.temperature_2m_min) ? daily.temperature_2m_min : [];

            const weatherByDate = new Map();
            for (let i = 0; i < times.length; i++) {
                weatherByDate.set(times[i], {
                    code: Number(codes[i] ?? 0),
                    max: Number(maxes[i] ?? 0),
                    min: Number(mins[i] ?? 0),
                });
            }

            const dates = targetDates();
            const daysMarkup = dates
                .map((daySpec) => `<td>${buildDayCell(daySpec, weatherByDate)}</td>`)
                .join('');

            return `
                <tr class="weather-row">
                    <th scope="row" class="weather-place">${location.name}</th>
                    ${daysMarkup}
                </tr>
            `;
        }

        async function paintWeather() {
            try {
                const rows = await Promise.all(locations.map((location) => fetchLocationForecast(location)));
                weatherTableBody.innerHTML = rows.join('');
            } catch (error) {
                weatherTableBody.innerHTML = '<tr><td colspan="5" class="weather-error-cell">Ezin izan da eguraldia kargatu. Saiatu berriro minutu batzuk barru.</td></tr>';
            }
        }

        paintWeather();
    })();
</script>
@endsection
