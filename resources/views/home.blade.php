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
            @endauth
        </div>
    </div>

    <div class="hero-card" id="home-hero-card">
        @php
            $heroPath = $heroPhoto?->image_path ?? '';
            $heroUrl = $heroPath !== ''
                ? (\Illuminate\Support\Str::startsWith($heroPath, ['http://', 'https://', '/'])
                    ? $heroPath
                    : '/storage/'.$heroPath)
                : 'https://images.unsplash.com/photo-1522163182402-834f871fd851?auto=format&fit=crop&w=1200&q=80';
            $heroAlt = $heroPhoto?->title ?? 'Eskalatzailea harkaitz horman';
        @endphp
        <img id="home-hero-image" src="{{ $heroUrl }}" alt="{{ $heroAlt }}">
        <div class="card-overlay">
            @if($heroPhoto)
                <h2 id="home-hero-title">{{ $heroPhoto->title }}</h2>
                <p id="home-hero-desc">{{ $heroPhoto->description ?? 'Sillarri komunitateko argazkia' }}</p>
            @else
                <h2 id="home-hero-title">Asteko bidea</h2>
                <p id="home-hero-desc">"Aresta del Vent" · 6c · 38m · Kareharri teknikoa</p>
            @endif
        </div>
    </div>
</section>

@php
    $heroGallery = \App\Models\MultimediaPhoto::query()->latest()->get();
    $heroGalleryPayload = $heroGallery->map(function ($photo) {
        $path = $photo->image_path;
        $url = \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/'])
            ? $path
            : '/storage/'.$path;
        return [
            'id' => $photo->id,
            'title' => $photo->title,
            'description' => $photo->description,
            'url' => $url,
        ];
    })->values();
@endphp

<a class="kilter-spotlight" href="{{ route('kilter') }}">
    <p class="eyebrow">Atal nagusia</p>
    <h2>KILTER</h2>
    <p>Plaka eta desplome saioak, ranking-a, entrenamendua eta asteko erronkak.</p>
    <span>KILTER atalera sartu</span>
</a>

<a class="denda-spotlight" href="{{ route('shop') }}">
    <p class="eyebrow">Denda</p>
    <h2>Sillarri Merch</h2>
    <p>Biserak, kamisetak eta sudaderiak. Estiloa eta eskalada, batera.</p>
    <span>Dendara joan</span>
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
                    <td colspan="5" class="weather-loading-cell">
                        <span class="loading-inline"><span class="spinner"></span>Eguraldia kargatzen...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section class="utilities-home">
    <div class="utilities-head">
        <p class="eyebrow">Utilitateak</p>
        <h3>Bideoak eta baliabideak</h3>
    </div>
    <div class="utilities-grid">
        @php
            $utilities = [
                [
                    'title' => 'Entrenamendu (PasoClave)',
                    'url' => 'https://www.pasoclave.com/entrenamiento/',
                    'note' => 'Entrenamendu gida eta aholkuak.',
                ],
                [
                    'title' => 'LeoMoves (YouTube)',
                    'url' => 'https://www.youtube.com/leomoves',
                    'note' => 'Mugimendu eta entreno bideoak.',
                ],
                [
                    'title' => 'Jordan Yeoh Fitness (YouTube)',
                    'url' => 'https://www.youtube.com/@jordanyeohfitness',
                    'note' => 'Indarra eta kondizio fisikoa.',
                ],
                [
                    'title' => 'Goma2 · Avereparazioa',
                    'url' => 'https://goma2.net/es/module/avereparacio/form',
                    'note' => 'Materialen berrikuntza eta konponketa.',
                ],
            ];
        @endphp
        @foreach($utilities as $utility)
            <a class="utilities-card" href="{{ $utility['url'] }}" target="_blank" rel="noopener noreferrer">
                <div class="utilities-thumb" aria-hidden="true">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M4 6h16v12H4z"></path>
                        <path d="M10 9l5 3-5 3z"></path>
                    </svg>
                </div>
                <div class="utilities-text">
                    <h4>{{ $utility['title'] }}</h4>
                    <p>{{ $utility['note'] }}</p>
                    <span class="utilities-link">{{ parse_url($utility['url'], PHP_URL_HOST) }}</span>
                </div>
            </a>
        @endforeach
    </div>
</section>

<div class="modal-shell hidden-modal" id="hero-gallery-modal" role="dialog" aria-modal="true" aria-labelledby="hero-gallery-title">
    <div class="modal-card modal-card-xl">
        <div class="modal-head">
            <h2 id="hero-gallery-title">Argazkia</h2>
            <button type="button" class="btn btn-secondary" id="hero-gallery-close">Itzuli</button>
        </div>
        <div class="media-lightbox-body">
            <img id="hero-gallery-image" alt="Argazkia">
            <div class="media-lightbox-text">
                <p id="hero-gallery-desc"></p>
                <div class="hero-gallery-controls">
                    <button type="button" class="btn btn-secondary" id="hero-gallery-prev">Aurrekoa</button>
                    <button type="button" class="btn btn-secondary" id="hero-gallery-next">Hurrengoa</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const heroImages = @json($heroGalleryPayload);
        const heroCard = document.getElementById('home-hero-card');
        const heroImage = document.getElementById('home-hero-image');
        const heroTitle = document.getElementById('home-hero-title');
        const heroDesc = document.getElementById('home-hero-desc');
        const initialHeroId = @json($heroPhoto?->id);
        let heroIndex = 0;
        const preloadCache = new Map();

        function buildRotationQueue(currentIndex) {
            const indices = heroImages.map((_, idx) => idx).filter((idx) => idx !== currentIndex);
            for (let i = indices.length - 1; i > 0; i -= 1) {
                const j = Math.floor(Math.random() * (i + 1));
                [indices[i], indices[j]] = [indices[j], indices[i]];
            }
            return indices;
        }

        function swapHero(index) {
            if (!heroImage || !heroImages.length) return;
            heroImage.classList.remove('is-visible');
            window.setTimeout(() => {
                const item = heroImages[index];
                heroImage.src = item.url;
                heroImage.alt = item.title || 'Argazkia';
                if (heroTitle) heroTitle.textContent = item.title || 'Argazkia';
                if (heroDesc) heroDesc.textContent = item.description || 'Sillarri komunitateko argazkia';
                heroImage.classList.add('is-visible');
            }, 260);
        }

        if (heroImages.length > 0 && heroImage) {
            let rotationQueue = [];
            const preloadImage = (index) => {
                if (!heroImages[index] || preloadCache.has(index)) return;
                const img = new Image();
                img.src = heroImages[index].url;
                preloadCache.set(index, img);
            };
            if (initialHeroId !== null) {
                const initialIndex = heroImages.findIndex((item) => item.id === initialHeroId);
                if (initialIndex >= 0) {
                    heroIndex = initialIndex;
                }
            }
            if (heroImages[heroIndex]) {
                const current = heroImages[heroIndex];
                if (heroTitle) heroTitle.textContent = current.title || 'Argazkia';
                if (heroDesc) heroDesc.textContent = current.description || 'Sillarri komunitateko argazkia';
            }
            heroImage.classList.add('is-visible');
            if (!rotationQueue.length) {
                rotationQueue = buildRotationQueue(heroIndex);
            }
            if (rotationQueue.length) {
                preloadImage(rotationQueue[0]);
            }
            window.setInterval(() => {
                if (heroImages.length <= 1) return;
                if (!rotationQueue.length) {
                    rotationQueue = buildRotationQueue(heroIndex);
                }
                const nextIndex = rotationQueue.shift();
                if (rotationQueue.length) {
                    preloadImage(rotationQueue[0]);
                }
                heroIndex = nextIndex;
                swapHero(heroIndex);
            }, 7000);
        }

        const heroModal = document.getElementById('hero-gallery-modal');
        const heroModalImage = document.getElementById('hero-gallery-image');
        const heroModalTitle = document.getElementById('hero-gallery-title');
        const heroModalDesc = document.getElementById('hero-gallery-desc');
        const heroPrev = document.getElementById('hero-gallery-prev');
        const heroNext = document.getElementById('hero-gallery-next');
        const heroClose = document.getElementById('hero-gallery-close');

        function openHeroModal(index) {
            if (!heroModal || !heroModalImage || !heroImages.length) return;
            heroIndex = index;
            const item = heroImages[heroIndex];
            heroModalImage.src = item.url;
            heroModalTitle.textContent = item.title || 'Argazkia';
            heroModalDesc.textContent = item.description || '';
            heroModal.classList.remove('hidden-modal');
        }

        function closeHeroModal() {
            heroModal?.classList.add('hidden-modal');
        }

        function stepHero(direction) {
            if (!heroImages.length) return;
            heroIndex = (heroIndex + direction + heroImages.length) % heroImages.length;
            openHeroModal(heroIndex);
        }

        heroCard?.addEventListener('click', () => openHeroModal(heroIndex));
        heroPrev?.addEventListener('click', () => stepHero(-1));
        heroNext?.addEventListener('click', () => stepHero(1));
        heroClose?.addEventListener('click', closeHeroModal);
        heroModal?.addEventListener('click', (event) => {
            if (event.target === heroModal) closeHeroModal();
        });

        const weatherBaseUrl = "{{ route('weather') }}";
        const locations = @json($weatherLocations->values());

        const weatherTableBody = document.getElementById('weatherTableBody');
        if (!weatherTableBody) return;
        if (!Array.isArray(locations) || locations.length === 0) {
            weatherTableBody.innerHTML = '<tr><td colspan="5" class="weather-error-cell">Ez dago kokapenik eguraldirako.</td></tr>';
            return;
        }

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
            const tomorrow = new Date(now);
            tomorrow.setDate(now.getDate() + 1);

            const results = [];
            const seen = new Set();

            function pushDate(date) {
                const key = formatDateKey(date);
                if (seen.has(key)) return;
                seen.add(key);
                results.push(date);
            }

            pushDate(today);
            pushDate(tomorrow);

            const todayDay = today.getDay();
            const tomorrowDay = tomorrow.getDay();
            const hasWeekend = todayDay === 6 || todayDay === 0 || tomorrowDay === 6 || tomorrowDay === 0;

            if (!hasWeekend) {
                const saturday = nextWeekday(today, 6);
                const sunday = nextWeekday(today, 0);
                pushDate(saturday);
                pushDate(sunday);
            }

            while (results.length < 4) {
                const next = new Date(results[results.length - 1]);
                next.setDate(next.getDate() + 1);
                pushDate(next);
            }

            return results.map((date, index) => ({ key: `d${index}`, date }));
        }

        function buildDayCell(daySpec, weatherByDate) {
            const data = weatherByDate.get(formatDateKey(daySpec.date));
            if (!data) {
                return '<span class="weather-pill weather-pill-empty">—</span>';
            }

            const href = `${weatherBaseUrl}?place=${encodeURIComponent(daySpec.place)}&date=${formatDateKey(daySpec.date)}`;
            return `
                <a class="weather-pill weather-pill-link" href="${href}">
                    <span class="weather-icon" aria-hidden="true">${iconFromWmo(data.code)}</span>
                    <span>${Math.round(data.max)}°/${Math.round(data.min)}°</span>
                </a>
            `;
        }

        async function fetchWithTimeout(url, timeoutMs = 8000) {
            const controller = new AbortController();
            const timeoutId = window.setTimeout(() => controller.abort(), timeoutMs);
            try {
                return await fetch(url, { method: 'GET', signal: controller.signal });
            } finally {
                window.clearTimeout(timeoutId);
            }
        }

        async function fetchLocationForecast(location) {
            const apiUrl = new URL('https://api.open-meteo.com/v1/forecast');
            apiUrl.searchParams.set('latitude', String(location.lat));
            apiUrl.searchParams.set('longitude', String(location.lon));
            apiUrl.searchParams.set('daily', 'weathercode,temperature_2m_max,temperature_2m_min');
            apiUrl.searchParams.set('forecast_days', '10');
            apiUrl.searchParams.set('timezone', 'Europe/Madrid');

            const response = await fetchWithTimeout(apiUrl.toString());
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
            const placeLabel = location.label || location.name;
            const daysMarkup = dates
                .map((daySpec) => `<td>${buildDayCell({ ...daySpec, place: placeLabel }, weatherByDate)}</td>`)
                .join('');

            return `
                <tr class="weather-row">
                    <th scope="row" class="weather-place">${placeLabel}</th>
                    ${daysMarkup}
                </tr>
            `;
        }

        let weatherRetryTimer = null;
        let weatherRetryAttempt = 0;

        function scheduleWeatherRetry() {
            if (weatherRetryTimer) return;
            weatherRetryAttempt = Math.min(weatherRetryAttempt + 1, 6);
            const delay = Math.min(15000 * weatherRetryAttempt, 90000);
            weatherRetryTimer = window.setTimeout(() => {
                weatherRetryTimer = null;
                paintWeather();
            }, delay);
        }

        async function paintWeather() {
            try {
                const results = await Promise.allSettled(
                    locations.map((location) => fetchLocationForecast(location))
                );
                const rows = results
                    .filter((result) => result.status === 'fulfilled' && result.value)
                    .map((result) => result.value);

                if (rows.length > 0) {
                    weatherTableBody.innerHTML = rows.join('');
                    weatherRetryAttempt = 0;
                    return;
                }

                weatherTableBody.innerHTML = '<tr><td colspan="5" class="weather-error-cell">Ez dago eguraldi daturik eskuragarri.</td></tr>';
                scheduleWeatherRetry();
            } catch (error) {
                weatherTableBody.innerHTML = '<tr><td colspan="5" class="weather-error-cell">Ezin izan da eguraldia kargatu. Saiatu berriro minutu batzuk barru.</td></tr>';
                scheduleWeatherRetry();
            }
        }

        paintWeather();
    })();
</script>
@endsection
