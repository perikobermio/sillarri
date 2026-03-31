@extends('layouts.app', ['title' => 'Eguraldia | Sillarri Climb'])

@section('content')
<section class="weather-detail">
    <div class="weather-detail-head">
        <p class="eyebrow">Eguraldiaren xehetasunak</p>
        <h1>Eguraldia orduz ordu</h1>
    </div>

    <div class="weather-detail-controls">
        <label>
            Herria
            <select id="weatherPlaceSelect"></select>
        </label>
        <label>
            Eguna
            <select id="weatherDateSelect"></select>
        </label>
        <div class="weather-detail-nav">
            <button type="button" class="btn btn-secondary" id="weatherPrevDay">Aurrekoa</button>
            <button type="button" class="btn btn-secondary" id="weatherNextDay">Hurrengoa</button>
        </div>
    </div>

    <div class="weather-detail-table-wrap">
        <table class="weather-detail-table">
            <thead>
                <tr>
                    <th>Ordua</th>
                    <th>⛅</th>
                    <th>Tenperatura</th>
                    <th>Sentsazioa</th>
                    <th>Euria</th>
                    <th>Euri aukera</th>
                    <th>Haizea</th>
                    <th>Norabidea</th>
                    <th>Hezetasuna</th>
                    <th>Hodeiak</th>
                </tr>
            </thead>
            <tbody id="weatherDetailBody">
                <tr>
                    <td colspan="9" class="weather-detail-loading">
                        <span class="loading-inline"><span class="spinner"></span>Eguraldia kargatzen...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<script>
    (function () {
        const locations = @json(collect($weatherLocations)->values());

        const placeSelect = document.getElementById('weatherPlaceSelect');
        const dateSelect = document.getElementById('weatherDateSelect');
        const body = document.getElementById('weatherDetailBody');
        const prevBtn = document.getElementById('weatherPrevDay');
        const nextBtn = document.getElementById('weatherNextDay');

        if (!placeSelect || !dateSelect || !body) return;

        const params = new URLSearchParams(window.location.search);
        const today = new Date();

        function formatDateKey(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function formatHourLabel(iso) {
            const time = iso.split('T')[1] || '';
            return time.slice(0, 5);
        }

        function degToCardinal(deg) {
            if (typeof deg !== 'number' || Number.isNaN(deg)) return '-';
            const dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
            const idx = Math.round(((deg % 360) / 45)) % 8;
            return dirs[idx];
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

        function buildDateOptions() {
            const options = [];
            for (let i = 0; i < 14; i++) {
                const d = new Date(today);
                d.setDate(today.getDate() + i);
                options.push({ label: formatDateKey(d), value: formatDateKey(d) });
            }
            return options;
        }

        function fillSelect(select, items, selectedValue) {
            select.innerHTML = '';
            items.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.value;
                option.textContent = item.label;
                if (item.value === selectedValue) option.selected = true;
                select.appendChild(option);
            });
        }

        function getLocationByName(name) {
            return locations.find((loc) => (loc.label || loc.name) === name) || locations[0];
        }

        function updateQuery(place, date) {
            const nextParams = new URLSearchParams(window.location.search);
            nextParams.set('place', place);
            nextParams.set('date', date);
            const newUrl = `${window.location.pathname}?${nextParams.toString()}`;
            window.history.replaceState({}, '', newUrl);
        }

        async function loadWeather(place, date) {
            const loc = getLocationByName(place);
            const apiUrl = new URL('https://api.open-meteo.com/v1/forecast');
            apiUrl.searchParams.set('latitude', String(loc.lat));
            apiUrl.searchParams.set('longitude', String(loc.lon));
            apiUrl.searchParams.set('hourly', [
                'weathercode',
                'temperature_2m',
                'apparent_temperature',
                'precipitation',
                'precipitation_probability',
                'windspeed_10m',
                'winddirection_10m',
                'relativehumidity_2m',
                'cloudcover',
            ].join(','));
            apiUrl.searchParams.set('timezone', 'Europe/Madrid');

            body.innerHTML = '<tr><td colspan="9" class="weather-detail-loading">Eguraldia kargatzen...</td></tr>';

            const response = await fetch(apiUrl.toString(), { method: 'GET' });
            if (!response.ok) {
                body.innerHTML = '<tr><td colspan="9" class="weather-detail-loading">Ezin izan da eguraldia kargatu.</td></tr>';
                return;
            }

            const json = await response.json();
            const hourly = json.hourly || {};
            const times = Array.isArray(hourly.time) ? hourly.time : [];
            const codes = Array.isArray(hourly.weathercode) ? hourly.weathercode : [];
            const temps = Array.isArray(hourly.temperature_2m) ? hourly.temperature_2m : [];
            const feels = Array.isArray(hourly.apparent_temperature) ? hourly.apparent_temperature : [];
            const rain = Array.isArray(hourly.precipitation) ? hourly.precipitation : [];
            const rainProb = Array.isArray(hourly.precipitation_probability) ? hourly.precipitation_probability : [];
            const wind = Array.isArray(hourly.windspeed_10m) ? hourly.windspeed_10m : [];
            const windDir = Array.isArray(hourly.winddirection_10m) ? hourly.winddirection_10m : [];
            const humidity = Array.isArray(hourly.relativehumidity_2m) ? hourly.relativehumidity_2m : [];
            const clouds = Array.isArray(hourly.cloudcover) ? hourly.cloudcover : [];

            const rows = [];
            const now = new Date();
            const windowStart = new Date(`${date}T00:00:00`);
            const windowEnd = new Date(windowStart);
            windowEnd.setDate(windowEnd.getDate() + 1);
            const dateIsToday = formatDateKey(now) === date;
            if (dateIsToday) {
                windowStart.setHours(now.getHours(), 0, 0, 0);
            }

            for (let i = 0; i < times.length; i++) {
                const iso = times[i];
                const stamp = new Date(iso);
                if (Number.isNaN(stamp.getTime())) continue;
                if (stamp < windowStart || stamp >= windowEnd) continue;
                rows.push(`
                    <tr>
                        <td>${formatHourLabel(iso)}</td>
                        <td>${iconFromWmo(codes[i])}</td>
                        <td>${Math.round(temps[i] ?? 0)}°</td>
                        <td>${Math.round(feels[i] ?? 0)}°</td>
                        <td>${(rain[i] ?? 0).toFixed(1)} mm</td>
                        <td>${Math.round(rainProb[i] ?? 0)}%</td>
                        <td>${Math.round(wind[i] ?? 0)} km/h</td>
                        <td>${degToCardinal(windDir[i])}</td>
                        <td>${Math.round(humidity[i] ?? 0)}%</td>
                        <td>${Math.round(clouds[i] ?? 0)}%</td>
                    </tr>
                `);
            }

            body.innerHTML = rows.length
                ? rows.join('')
                : '<tr><td colspan="9" class="weather-detail-loading">Ez dago daturik egun honetarako.</td></tr>';
        }

        const defaultLabel = locations[0]?.label || locations[0]?.name;
        const placeValue = params.get('place') || defaultLabel;
        const dateValue = params.get('date') || formatDateKey(today);

        fillSelect(placeSelect, locations.map((l) => ({ label: l.label || l.name, value: l.label || l.name })), placeValue);
        fillSelect(dateSelect, buildDateOptions(), dateValue);
        updateQuery(placeValue, dateValue);
        loadWeather(placeValue, dateValue);

        placeSelect.addEventListener('change', () => {
            const place = placeSelect.value;
            const date = dateSelect.value;
            updateQuery(place, date);
            loadWeather(place, date);
        });

        dateSelect.addEventListener('change', () => {
            const place = placeSelect.value;
            const date = dateSelect.value;
            updateQuery(place, date);
            loadWeather(place, date);
        });

        prevBtn.addEventListener('click', () => {
            const options = Array.from(dateSelect.options);
            const index = options.findIndex((opt) => opt.value === dateSelect.value);
            if (index > 0) {
                dateSelect.value = options[index - 1].value;
                dateSelect.dispatchEvent(new Event('change'));
            }
        });

        nextBtn.addEventListener('click', () => {
            const options = Array.from(dateSelect.options);
            const index = options.findIndex((opt) => opt.value === dateSelect.value);
            if (index < options.length - 1) {
                dateSelect.value = options[index + 1].value;
                dateSelect.dispatchEvent(new Event('change'));
            }
        });
    })();
</script>
@endsection
