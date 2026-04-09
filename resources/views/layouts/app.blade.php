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
                <a id="headerWeatherTicker" class="header-live-text" href="{{ route('home') }}#home-weather">
                    <span class="loading-inline"><span class="spinner"></span>Eguraldia kargatzen...</span>
                </a>
            </div>
            <div class="net-status" id="netStatus" aria-live="polite" aria-hidden="true">
                <span class="net-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" role="img" aria-label="Konexiorik ez">
                        <path d="M2.7 6.4c5-4 13.6-4 18.6 0l-2.1 2.1c-4-3-10.4-3-14.4 0L2.7 6.4z" fill="currentColor"/>
                        <path d="M6.9 10.6c2.9-2.3 7.3-2.3 10.2 0l-2.1 2.1c-1.7-1.3-4.3-1.3-6 0l-2.1-2.1z" fill="currentColor"/>
                        <path d="M11.3 15l-2.3 2.3 3 3 3-3-2.3-2.3c-.2-.2-.6-.2-.8 0z" fill="currentColor"/>
                        <path d="M3 3l18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="net-label">Konexiorik ez</span>
            </div>
        </div>

        <nav class="nav-links">
            <a class="kilter-nav" href="{{ route('kilter') }}">KILTER</a>
            <a class="desktop-only" href="{{ route('ranking') }}">Sailkapena</a>
            <a class="desktop-only" href="{{ route('shop') }}">Denda</a>
            @if(request()->routeIs('kilter*'))
                <a class="mobile-only" href="{{ route('ranking') }}">Sailkapena</a>
            @else
                <a class="mobile-only" href="{{ route('shop') }}">Denda</a>
            @endif
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
                        <a href="{{ route('multimedia') }}">Multimedia</a>
                        <a href="{{ route('settings') }}">Ezarpenak</a>
                        @if((bool) auth()->user()->is_admin)
                            <a href="{{ route('admin') }}">Admin</a>
                        @endif
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
        @if(session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <div class="app-snackbar" id="appSnackbar" role="status" aria-live="polite"></div>
    <div class="interaction-blocker" id="interactionBlocker" aria-hidden="true"></div>

    <script>
        (function () {
            const snackbar = document.getElementById('appSnackbar');
            const blocker = document.getElementById('interactionBlocker');
            let lastMessage = '';
            let lastTime = 0;
            let loadingCount = 0;

            function setGlobalBlocking(active) {
                if (!blocker) return;
                blocker.classList.toggle('is-active', active);
                document.documentElement.classList.toggle('is-busy', active);
            }

            window.setGlobalLoading = function (isLoading) {
                if (isLoading) {
                    loadingCount += 1;
                } else {
                    loadingCount = Math.max(0, loadingCount - 1);
                }
                setGlobalBlocking(loadingCount > 0);
            };

            window.setButtonLoading = function (el, isLoading) {
                if (!el) return;
                const alreadyActive = el.dataset.loadingActive === 'true';
                if (isLoading) {
                    if (!alreadyActive) {
                        el.dataset.loadingActive = 'true';
                        window.setGlobalLoading?.(true);
                    }
                    if (!el.disabled) {
                        el.dataset.loadingDisabled = 'true';
                        el.disabled = true;
                    }
                    el.classList.add('is-loading');
                    el.setAttribute('aria-busy', 'true');
                } else {
                    if (alreadyActive) {
                        delete el.dataset.loadingActive;
                        window.setGlobalLoading?.(false);
                    }
                    el.classList.remove('is-loading');
                    el.removeAttribute('aria-busy');
                    if (el.dataset.loadingDisabled === 'true') {
                        if (el.dataset.offlineDisabled !== 'true') {
                            el.disabled = false;
                        }
                        delete el.dataset.loadingDisabled;
                    }
                }
            };

            window.showSnackbar = function (message, options = {}) {
                if (!snackbar || !message) return;
                const now = Date.now();
                const key = options.key || message;
                if (lastMessage === key && now - lastTime < 4000) return;
                lastMessage = key;
                lastTime = now;
                snackbar.textContent = message;
                snackbar.classList.add('is-visible');
                window.clearTimeout(window.showSnackbar._timer);
                window.showSnackbar._timer = window.setTimeout(() => {
                    snackbar.classList.remove('is-visible');
                }, options.duration || 2400);
            };

            window.appFetch = async function (url, options = {}) {
                const timeoutMs = options.timeoutMs ?? 8000;
                const showError = options.showError ?? true;
                const errorMessage = options.errorMessage || 'Sareko errorea gertatu da.';
                const controller = new AbortController();
                const timeoutId = window.setTimeout(() => controller.abort(), timeoutMs);
                const opts = { ...options, signal: controller.signal };
                const loadingEl = opts.loadingEl || (opts.loadingSelector ? document.querySelector(opts.loadingSelector) : null);
                delete opts.timeoutMs;
                delete opts.showError;
                delete opts.errorMessage;
                delete opts.loadingEl;
                delete opts.loadingSelector;
                if (loadingEl) window.setButtonLoading?.(loadingEl, true);
                try {
                    return await fetch(url, opts);
                } catch (error) {
                    if (showError) window.showSnackbar?.(errorMessage);
                    throw error;
                } finally {
                    window.clearTimeout(timeoutId);
                    if (loadingEl) window.setButtonLoading?.(loadingEl, false);
                }
            };
        })();

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
            const locations = @json(\App\Models\WeatherLocation::query()->orderBy('name')->get()->values());

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

            async function fetchWithTimeout(url, timeoutMs = 8000) {
                if (window.appFetch) {
                    return window.appFetch(url, { method: 'GET', timeoutMs, showError: false });
                }
                const controller = new AbortController();
                const timeoutId = window.setTimeout(() => controller.abort(), timeoutMs);
                try {
                    return await fetch(url, { method: 'GET', signal: controller.signal });
                } finally {
                    window.clearTimeout(timeoutId);
                }
            }

            async function buildEntries() {
                const dates = targetDates();
                if (!Array.isArray(locations) || locations.length === 0) {
                    return [];
                }
                let hadError = false;
                const results = await Promise.allSettled(
                    locations.map(async (location) => {
                        const apiUrl = new URL('https://api.open-meteo.com/v1/forecast');
                        apiUrl.searchParams.set('latitude', String(location.lat));
                        apiUrl.searchParams.set('longitude', String(location.lon));
                        apiUrl.searchParams.set('daily', 'weathercode,temperature_2m_max,temperature_2m_min');
                        apiUrl.searchParams.set('forecast_days', '10');
                        apiUrl.searchParams.set('timezone', 'Europe/Madrid');

                        try {
                            const response = await fetchWithTimeout(apiUrl.toString());
                            if (!response.ok) return null;
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
                            if (!item) return null;
                            const placeLabel = location.label || location.name;
                            return {
                                text: `${placeLabel} · ${iconFromWmo(item.code)} ${Math.round(item.max)}°/${Math.round(item.min)}°`,
                                href: `${weatherBaseUrl}?place=${encodeURIComponent(placeLabel)}&date=${today.key}`,
                            };
                        } catch (error) {
                            hadError = true;
                            return null;
                        }
                    })
                );

                if (hadError) {
                    window.showSnackbar?.('Eguraldiaren datuak ezin izan dira kargatu.');
                }

                return results
                    .filter((result) => result.status === 'fulfilled' && result.value)
                    .map((result) => result.value);
            }

            function showWithFade(entry) {
                ticker.classList.remove('is-visible');
                window.setTimeout(() => {
                    ticker.textContent = entry.text;
                    ticker.href = entry.href;
                    ticker.classList.add('is-visible');
                }, 220);
            }

            let retryTimer = null;
            let rotationTimer = null;
            let retryAttempt = 0;

            function scheduleRetry() {
                if (retryTimer) return;
                retryAttempt = Math.min(retryAttempt + 1, 6);
                const delay = Math.min(15000 * retryAttempt, 90000);
                retryTimer = window.setTimeout(() => {
                    retryTimer = null;
                    loadTicker();
                }, delay);
            }

            function loadTicker() {
                buildEntries()
                    .then((entries) => {
                        if (!entries.length) {
                            ticker.textContent = 'Eguraldi daturik ez une honetan';
                            ticker.classList.add('is-visible');
                            scheduleRetry();
                            return;
                        }

                        retryAttempt = 0;
                        if (rotationTimer) {
                            window.clearInterval(rotationTimer);
                            rotationTimer = null;
                        }

                        let index = 0;
                        showWithFade(entries[index]);
                        rotationTimer = window.setInterval(() => {
                            index = (index + 1) % entries.length;
                            showWithFade(entries[index]);
                        }, 6000);
                    })
                    .catch(() => {
                        ticker.textContent = 'Eguraldi daturik ez une honetan';
                        ticker.classList.add('is-visible');
                        scheduleRetry();
                    });
            }

            loadTicker();
        })();

        (function () {
            const statusEl = document.getElementById('netStatus');
            if (!statusEl) return;
            const labelEl = statusEl.querySelector('.net-label');
            const iconEl = statusEl.querySelector('.net-icon');

            function collectSubmitters() {
                const submitters = [];
                document.querySelectorAll('form').forEach((form) => {
                    const method = (form.getAttribute('method') || 'GET').toUpperCase();
                    if (method === 'GET') return;
                    form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((el) => {
                        submitters.push(el);
                    });
                });
                return submitters;
            }

            function setOfflineDisabled(isOffline) {
                document.documentElement.classList.toggle('is-offline', isOffline);

                const submitters = collectSubmitters();
                submitters.forEach((el) => {
                    if (isOffline) {
                        if (!el.disabled) {
                            el.dataset.offlineDisabled = 'true';
                            el.disabled = true;
                            el.setAttribute('title', 'Konexiorik ez');
                        }
                    } else if (el.dataset.offlineDisabled === 'true') {
                        el.disabled = false;
                        el.removeAttribute('title');
                        delete el.dataset.offlineDisabled;
                    }
                });
            }

            function updateStatus() {
                const online = navigator.onLine !== false;
                statusEl.classList.toggle('is-offline', !online);
                statusEl.setAttribute('aria-hidden', online ? 'true' : 'false');
                statusEl.setAttribute('title', online ? '' : 'Konexiorik ez');
                if (labelEl) labelEl.textContent = 'Konexiorik ez';
                if (iconEl) iconEl.setAttribute('aria-label', 'Konexiorik ez');
                setOfflineDisabled(!online);
            }

            window.addEventListener('online', updateStatus);
            window.addEventListener('offline', updateStatus);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) updateStatus();
            });
            updateStatus();
        })();

        (function () {
            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (!form || !(form instanceof HTMLFormElement)) return;
                if (form.dataset.noLoading === 'true') return;
                let submitter = event.submitter;
                if (!submitter) {
                    const candidates = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    if (candidates.length === 1) submitter = candidates[0];
                }
                if (!submitter || submitter.dataset.noLoading === 'true') return;
                window.setButtonLoading?.(submitter, true);
            });
        })();
    </script>
</body>
</html>
