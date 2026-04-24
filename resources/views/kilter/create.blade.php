@extends('layouts.app', ['title' => 'Blokea sortu | KILTER'])

@section('content')
<section class="kilter-page">
    <div class="kilter-form-wrap">
        <p class="eyebrow">Kilter Board Hub</p>
        <h1>BLOKEA SORTU</h1>

        <form method="POST" action="{{ route('kilter.store') }}" class="kilter-form" enctype="multipart/form-data">
            @csrf

            <label>Blokearen izena</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Deskribapena</label>
            <textarea name="description" rows="4" required>{{ old('description') }}</textarea>
            @error('description')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Gradua</label>
            <select name="grade" required>
                <option value="">Hautatu gradua</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade }}" @selected(old('grade') === $grade)>{{ $grade }}</option>
                @endforeach
            </select>
            @error('grade')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Kokapena</label>
            <div class="map-picker-row">
                <select name="kokapena" id="kokapena-select" required>
                    <option value="">Hautatu kokapena</option>
                    @foreach($locations as $location)
                        <option value="{{ $location }}" @selected(old('kokapena') === $location)>{{ $location }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-secondary map-upload-toggle" id="open-location-modal">
                    <span class="map-upload-toggle-label">Kokapena gehitu</span>
                </button>
            </div>
            @error('kokapena')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Mapa</label>
            <div class="map-picker-row">
                <select name="map_id" id="map-select" class="map-select-hidden" required>
                    <option value="">Hautatu mapa bat</option>
                    @foreach($maps as $map)
                        @php
                            $imageUrl = '';
                            if ($map->image) {
                                $imageUrl = \Illuminate\Support\Str::startsWith($map->image, ['http://', 'https://', '/'])
                                    ? $map->image
                                    : '/storage/'.$map->image;
                            }
                        @endphp
                        <option
                            value="{{ $map->id }}"
                            data-kokapena="{{ $map->kokapena }}"
                            data-image-url="{{ $imageUrl }}"
                            @selected((string) old('map_id') === (string) $map->id)
                        >
                            {{ $map->name }} (ID {{ $map->id }})
                        </option>
                    @endforeach
                </select>
                <div class="map-picker" id="map-picker">
                    <button type="button" class="map-picker-trigger" id="map-picker-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <img class="map-picker-thumb is-hidden" id="map-picker-thumb" alt="">
                        <span id="map-picker-label">Hautatu mapa bat</span>
                        <span class="map-picker-caret" aria-hidden="true">▾</span>
                    </button>
                    <div class="map-picker-list" id="map-picker-list" role="listbox" aria-label="Mapak">
                        @foreach($maps as $map)
                            @php
                                $imageUrl = '';
                                if ($map->image) {
                                    $imageUrl = \Illuminate\Support\Str::startsWith($map->image, ['http://', 'https://', '/'])
                                        ? $map->image
                                        : '/storage/'.$map->image;
                                }
                                $label = $map->name.' (ID '.$map->id.')';
                            @endphp
                            <button
                                type="button"
                                class="map-picker-option"
                                data-map-id="{{ $map->id }}"
                                data-kokapena="{{ $map->kokapena }}"
                                data-image-url="{{ $imageUrl }}"
                                data-label="{{ $label }}"
                                role="option"
                                aria-selected="{{ (string) old('map_id') === (string) $map->id ? 'true' : 'false' }}"
                            >
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $map->name }}">
                                @else
                                    <div class="map-picker-thumb map-picker-placeholder" aria-hidden="true">?</div>
                                @endif
                                <span>{{ $label }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <button type="button" class="btn btn-secondary map-upload-toggle" id="open-map-modal">
                    <span class="map-upload-toggle-label">Mapa gehitu</span>
                </button>
            </div>
            @error('map_id')
                <small class="error">{{ $message }}</small>
            @enderror

            <input type="hidden" name="boulder" id="boulder-input" value="{{ old('boulder') }}">
            <div class="boulder-row">
                <button type="button" class="btn btn-secondary" id="open-boulder-modal">Koordenatuak zehaztu</button>
            </div>
            @error('boulder')
                <small class="error">{{ $message }}</small>
            @enderror

        <div class="kilter-form-actions">
            <button type="submit" class="btn btn-primary btn-icon" aria-label="Blokea gorde" title="Blokea gorde">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 4h13l3 3v13H4z"/>
                    <path d="M8 4v6h8V4"/>
                    <path d="M7 20v-6h10v6"/>
                </svg>
            </button>
            <a href="{{ route('kilter') }}" class="btn btn-secondary btn-icon" aria-label="Utzi" title="Utzi">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 6l12 12"/>
                    <path d="M18 6l-12 12"/>
                </svg>
            </a>
        </div>
        </form>
    </div>
</section>

<div class="modal-shell hidden-modal" id="map-modal" role="dialog" aria-modal="true" aria-labelledby="map-modal-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="map-modal-title">Mapa gehitu</h2>
            <button type="button" class="icon-btn" id="close-map-modal" aria-label="Itxi leihoa">×</button>
        </div>

        <form id="map-create-form" class="kilter-form" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <label>Maparen izena</label>
            <input type="text" name="name" id="map-name" required>

            <label class="map-upload-label">Irudia hautatu</label>
            <input type="file" name="image" id="map-image-file" accept="image/*" required>

            <small class="error hidden-error" id="map-modal-error"></small>

            <div class="kilter-form-actions">
                <button type="submit" class="btn btn-primary" id="save-map-btn"><span class="btn-text">Mapa gorde</span></button>
                <button type="button" class="btn btn-secondary" id="cancel-map-modal">Utzi</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-shell hidden-modal" id="location-modal" role="dialog" aria-modal="true" aria-labelledby="location-modal-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="location-modal-title">Kokapena gehitu</h2>
            <button type="button" class="icon-btn" id="close-location-modal" aria-label="Itxi leihoa">×</button>
        </div>

        <form id="location-create-form" class="kilter-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <label>Kokapenaren izena</label>
            <input type="text" name="name" id="location-name" required>

            <small class="error hidden-error" id="location-modal-error"></small>

            <div class="kilter-form-actions">
                <button type="submit" class="btn btn-primary" id="save-location-btn"><span class="btn-text">Kokapena gorde</span></button>
                <button type="button" class="btn btn-secondary" id="cancel-location-modal">Utzi</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-shell hidden-modal" id="boulder-modal" role="dialog" aria-modal="true" aria-labelledby="boulder-modal-title">
    <div class="modal-card modal-card-xl">
        <div class="modal-head">
            <h2 id="boulder-modal-title">BOULDER-a SORTU</h2>
            <button type="button" class="icon-btn" id="close-boulder-modal" aria-label="Itxi leihoa">×</button>
        </div>

        <div class="coord-toolbar">
            <span class="coord-option-wrap">
                <label for="coord-mode">Marrazki mota</label>
                <select id="coord-mode">
                <option value="points" selected>Zirkuluak</option>
                <option value="trave">Trave</option>
                <option value="line">Lerroa</option>
            </select>
            </span>
            <span id="point-type-wrap" class="coord-option-wrap">
                <label for="point-type">Mota</label>
                <select id="point-type">
                    <option value="pie">Horia - oina</option>
                    <option value="mano_pie" selected>Urdina - eskua/oina</option>
                    <option value="comienzo">Arrosa - hasiera</option>
                    <option value="top">Gorria - topa</option>
                </select>
            </span>
            <span id="point-size-wrap" class="coord-option-wrap">
                <label for="point-size">Tamaina</label>
                <select id="point-size">
                    <option value="pequeno">Txikia</option>
                    <option value="mediano" selected>Ertaina</option>
                    <option value="grande">Handia</option>
                    <option value="gigante">Erraldoia</option>
                </select>
            </span>
        </div>

        <div class="coord-canvas-wrap" id="coord-canvas-wrap">
            <div class="coord-stage" id="coord-stage">
                <img id="coord-image" alt="Koordenatuetarako mapa">
                <div id="coord-layer"></div>
            </div>
        </div>

        <small class="error hidden-error" id="boulder-modal-error"></small>

        <div class="kilter-form-actions">
            <button type="button" class="btn btn-primary btn-icon" id="save-boulder-points" aria-label="Koordenatuak gorde" title="Koordenatuak gorde">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 4h13l3 3v13H4z"/>
                    <path d="M8 4v6h8V4"/>
                    <path d="M7 20v-6h10v6"/>
                </svg>
            </button>
            <button type="button" class="btn btn-secondary btn-icon" id="clear-points" aria-label="Puntuak garbitu" title="Puntuak garbitu">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 6h18"/>
                    <path d="M8 6V4h8v2"/>
                    <path d="M6 6l1 14h10l1-14"/>
                </svg>
            </button>
            <button type="button" class="btn btn-secondary btn-icon" id="cancel-boulder-modal" aria-label="Utzi" title="Utzi">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 6l12 12"/>
                    <path d="M18 6l-12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<div id="app-snackbar" class="app-snackbar" role="status" aria-live="polite"></div>

<script>
    (function () {
        const locationSelect = document.getElementById('kokapena-select');
        const mapSelect = document.getElementById('map-select');
        const mapPicker = document.getElementById('map-picker');
        const mapPickerTrigger = document.getElementById('map-picker-trigger');
        const mapPickerList = document.getElementById('map-picker-list');
        const mapPickerLabel = document.getElementById('map-picker-label');
        const mapPickerThumb = document.getElementById('map-picker-thumb');
        const boulderInput = document.getElementById('boulder-input');
        const openBoulderBtn = document.getElementById('open-boulder-modal');
        const validModes = ['points', 'line', 'trave'];
        const validTypes = ['pie', 'mano_pie', 'comienzo', 'top'];
        const validSizes = ['pequeno', 'mediano', 'grande', 'gigante'];

        function normalizePoint(point, mode) {
            if (mode === 'line') {
                return {
                    x: Number(point?.x ?? 0),
                    y: Number(point?.y ?? 0),
                };
            }

            return {
                x: Number(point?.x ?? 0),
                y: Number(point?.y ?? 0),
                type: validTypes.includes(point?.type) ? point.type : 'mano_pie',
                size: validSizes.includes(point?.size) ? point.size : 'mediano',
            };
        }

        function applyTraveOrder(points) {
            let nextOrder = 0;

            return points.map((point) => {
                const normalized = normalizePoint(point, 'trave');
                if (normalized.type === 'mano_pie') {
                    nextOrder += 1;
                    normalized.order = nextOrder;
                }
                return normalized;
            });
        }

        function normalizePointCollection(points, mode) {
            if (!Array.isArray(points)) {
                return [];
            }

            if (mode === 'line') {
                return points.map((point) => normalizePoint(point, 'line'));
            }

            const normalizedPoints = points.map((point) => normalizePoint(point, mode));
            return mode === 'trave' ? applyTraveOrder(normalizedPoints) : normalizedPoints;
        }

        function normalizeBoulderState(payload) {
            if (Array.isArray(payload)) {
                return {
                    mode: 'points',
                    points: normalizePointCollection(payload, 'points'),
                };
            }

            if (payload && typeof payload === 'object' && Array.isArray(payload.points)) {
                const mode = validModes.includes(payload.mode) ? payload.mode : 'points';
                return {
                    mode,
                    points: normalizePointCollection(payload.points, mode),
                };
            }

            return { mode: 'points', points: [] };
        }

        function updateMapPicker(label, imageUrl) {
            if (mapPickerLabel) {
                mapPickerLabel.textContent = label || 'Hautatu mapa bat';
            }
            if (!mapPickerThumb) return;
            if (imageUrl) {
                mapPickerThumb.src = imageUrl;
                mapPickerThumb.classList.remove('is-hidden');
            } else {
                mapPickerThumb.removeAttribute('src');
                mapPickerThumb.classList.add('is-hidden');
            }
        }

        function normalizeLocationValue(value) {
            return String(value || '').trim().toLowerCase();
        }

        function syncMapPickerFromSelect() {
            if (!mapSelect) return;
            const selected = mapSelect.options[mapSelect.selectedIndex];
            if (!selected || !selected.value) {
                updateMapPicker('Hautatu mapa bat', '');
                return;
            }
            updateMapPicker(selected.textContent?.trim() || 'Hautatu mapa bat', selected.dataset?.imageUrl || '');
            mapPickerList?.querySelectorAll('.map-picker-option').forEach((btn) => {
                btn.setAttribute('aria-selected', btn.dataset.mapId === selected.value ? 'true' : 'false');
            });
        }

        function filterMapsByLocation() {
            const selectedLocation = normalizeLocationValue(locationSelect?.value || '');
            let hasVisibleSelection = false;

            Array.from(mapSelect?.options || []).forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    option.disabled = false;
                    return;
                }

                const isVisible = selectedLocation !== '' && normalizeLocationValue(option.dataset.kokapena) === selectedLocation;
                option.hidden = !isVisible;
                option.disabled = !isVisible;
                if (isVisible && option.selected) {
                    hasVisibleSelection = true;
                }
            });

            mapPickerList?.querySelectorAll('.map-picker-option').forEach((button) => {
                const isVisible = selectedLocation !== '' && normalizeLocationValue(button.dataset.kokapena) === selectedLocation;
                button.hidden = !isVisible;
                button.classList.toggle('is-hidden-map-option', !isVisible);
                button.setAttribute('aria-hidden', !isVisible ? 'true' : 'false');
            });

            if (!hasVisibleSelection) {
                mapSelect.value = '';
            }

            if (mapPickerLabel && selectedLocation === '') {
                updateMapPicker('Lehenengo kokapena hautatu', '');
            } else {
                syncMapPickerFromSelect();
            }
        }

        function closeMapPicker() {
            if (!mapPickerTrigger || !mapPickerList) return;
            mapPickerList.classList.remove('is-open');
            mapPickerTrigger.setAttribute('aria-expanded', 'false');
        }

        function toggleMapPicker() {
            if (!mapPickerTrigger || !mapPickerList) return;
            if (!locationSelect?.value) {
                updateMapPicker('Lehenengo kokapena hautatu', '');
                return;
            }
            const isOpen = mapPickerList.classList.contains('is-open');
            if (isOpen) {
                closeMapPicker();
            } else {
                const hasVisibleOptions = Array.from(mapPickerList.querySelectorAll('.map-picker-option')).some((option) => !option.hidden);
                if (!hasVisibleOptions) {
                    updateMapPicker('Ez dago maparik kokapena horretarako', '');
                    return;
                }
                mapPickerList.classList.add('is-open');
                mapPickerTrigger.setAttribute('aria-expanded', 'true');
            }
        }

        function bindMapPickerOptions(container) {
            if (!container) return;
            container.querySelectorAll('.map-picker-option').forEach((option) => {
                if (option.dataset.bound === 'true') {
                    return;
                }
                option.dataset.bound = 'true';
                option.addEventListener('click', () => {
                    const mapId = option.dataset.mapId || '';
                    if (!mapSelect || !mapId) return;
                    mapSelect.value = mapId;
                    mapSelect.dispatchEvent(new Event('change'));
                    mapPickerList?.querySelectorAll('.map-picker-option').forEach((btn) => {
                        btn.setAttribute('aria-selected', btn === option ? 'true' : 'false');
                    });
                    updateMapPicker(option.dataset.label || option.textContent?.trim(), option.dataset.imageUrl || '');
                    closeMapPicker();
                });
            });
        }

        mapPickerTrigger?.addEventListener('click', (event) => {
            event.preventDefault();
            toggleMapPicker();
        });

        document.addEventListener('click', (event) => {
            if (!mapPicker || !mapPickerList) return;
            if (!mapPicker.contains(event.target)) {
                closeMapPicker();
            }
        });

        mapSelect?.addEventListener('change', () => {
            syncMapPickerFromSelect();
        });
        locationSelect?.addEventListener('change', () => {
            filterMapsByLocation();
        });

        syncBoulderButtonState();

        function parseBoulderState() {
            try {
                const parsed = JSON.parse(boulderInput.value || '[]');
                return normalizeBoulderState(parsed);
            } catch {
                return { mode: 'points', points: [] };
            }
        }

        function syncBoulderButtonState() {
            if (!openBoulderBtn) return;
            const state = parseBoulderState();
            const hasPoints = Array.isArray(state.points) && state.points.length > 0;
            openBoulderBtn.classList.toggle('has-points', hasPoints);
        }


        function syncBodyScrollLock() {
            const hasOpenModal = document.querySelector('.modal-shell:not(.hidden-modal)') !== null;
            document.body.style.overflow = hasOpenModal ? 'hidden' : '';
        }


        // Modal de mapa
        const mapModal = document.getElementById('map-modal');
        const locationModal = document.getElementById('location-modal');
        const openMapBtn = document.getElementById('open-map-modal');
        const openLocationBtn = document.getElementById('open-location-modal');
        const closeMapBtn = document.getElementById('close-map-modal');
        const closeLocationBtn = document.getElementById('close-location-modal');
        const cancelMapBtn = document.getElementById('cancel-map-modal');
        const cancelLocationBtn = document.getElementById('cancel-location-modal');
        const mapForm = document.getElementById('map-create-form');
        const locationForm = document.getElementById('location-create-form');
        const mapError = document.getElementById('map-modal-error');
        const locationError = document.getElementById('location-modal-error');
        const saveMapBtn = document.getElementById('save-map-btn');
        const saveLocationBtn = document.getElementById('save-location-btn');
        const mapFileInput = document.getElementById('map-image-file');
        const appSnackbar = document.getElementById('app-snackbar');
        const maxMapFileBytes = 20 * 1024 * 1024;
        let snackbarTimeoutId = null;

        function showSnackbar(message) {
            if (!appSnackbar) return;
            if (snackbarTimeoutId) {
                clearTimeout(snackbarTimeoutId);
            }
            appSnackbar.textContent = message;
            appSnackbar.classList.add('is-visible');
            snackbarTimeoutId = setTimeout(() => {
                appSnackbar.classList.remove('is-visible');
            }, 4200);
        }

        const openMapModal = () => {
            if (!locationSelect?.value) {
                showSnackbar('Lehenengo kokapena hautatu behar duzu.');
                return;
            }
            mapModal.classList.remove('hidden-modal');
            syncBodyScrollLock();
        };
        const closeMapModal = () => {
            mapModal.classList.add('hidden-modal');
            mapError.classList.add('hidden-error');
            mapError.textContent = '';
            mapForm.reset();
            syncBodyScrollLock();
        };
        const openLocationModal = () => {
            locationModal.classList.remove('hidden-modal');
            syncBodyScrollLock();
        };
        const closeLocationModal = () => {
            locationModal.classList.add('hidden-modal');
            locationError.classList.add('hidden-error');
            locationError.textContent = '';
            locationForm.reset();
            syncBodyScrollLock();
        };

        openMapBtn?.addEventListener('click', openMapModal);
        openLocationBtn?.addEventListener('click', openLocationModal);
        closeMapBtn?.addEventListener('click', closeMapModal);
        closeLocationBtn?.addEventListener('click', closeLocationModal);
        cancelMapBtn?.addEventListener('click', closeMapModal);
        cancelLocationBtn?.addEventListener('click', closeLocationModal);
        mapModal?.addEventListener('click', (event) => {
            if (event.target === mapModal) closeMapModal();
        });
        locationModal?.addEventListener('click', (event) => {
            if (event.target === locationModal) closeLocationModal();
        });

        mapForm?.addEventListener('submit', async (event) => {
            event.preventDefault();

            mapError.classList.add('hidden-error');
            mapError.textContent = '';
            window.setButtonLoading?.(saveMapBtn, true);

            const payload = new FormData();
            payload.append('_token', mapForm.querySelector('input[name="_token"]').value);
            payload.append('name', mapForm.querySelector('#map-name').value);
            payload.append('kokapena', locationSelect?.value || '');

            const file = mapFileInput?.files?.[0];
            const chosenFile = file;

            if (!chosenFile) {
                mapError.textContent = 'Irudi bat hautatu behar duzu.';
                mapError.classList.remove('hidden-error');
                showSnackbar('Irudi bat hautatu behar duzu.');
                window.setButtonLoading?.(saveMapBtn, false);
                return;
            }

            if (chosenFile.size > maxMapFileBytes) {
                mapError.textContent = 'Irudia handiegia da. Gehienez 20MB onartzen dira.';
                mapError.classList.remove('hidden-error');
                showSnackbar('Irudia handiegia da. Gehienez 20MB onartzen dira.');
                window.setButtonLoading?.(saveMapBtn, false);
                return;
            }

            payload.append('image', chosenFile);

            try {
                const requestOptions = {
                    method: 'POST',
                    body: payload,
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': mapForm.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                };
                const response = window.appFetch
                    ? await window.appFetch('{{ route('kilter.maps.store') }}', { ...requestOptions, timeoutMs: 20000, showError: false })
                    : await fetch('{{ route('kilter.maps.store') }}', requestOptions);

                const contentType = response.headers.get('content-type') || '';
                let data = null;
                let rawText = '';

                if (contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    rawText = await response.text();
                }

                if (!response.ok) {
                    let msg = data?.message || `Errorea ${response.status} mapa gordetzean.`;
                    if (data?.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        const firstError = firstKey ? data.errors[firstKey]?.[0] : null;
                        if (firstError) msg = firstError;
                    }
                    if (response.status === 419) {
                        msg = 'Saioa iraungi da (419). Berritu orria eta saiatu berriro.';
                    } else if (response.status === 413) {
                        msg = 'Irudia handiegia da zerbitzarirako (413). Gehienez 20MB.';
                    } else if (!data && rawText) {
                        msg = `Errorea ${response.status}. Berrikusi zerbitzariaren logak.`;
                    }
                    mapError.textContent = msg;
                    mapError.classList.remove('hidden-error');
                    showSnackbar(msg);
                    window.setButtonLoading?.(saveMapBtn, false);
                    return;
                }

                const option = document.createElement('option');
                option.value = data.id;
                option.textContent = `${data.name} (ID ${data.id})`;
                option.dataset.kokapena = data.kokapena || '';
                option.dataset.imageUrl = data.image_url || '';
                option.selected = true;
                mapSelect.appendChild(option);
                mapSelect.value = String(data.id);

                if (mapPickerList) {
                    const optionBtn = document.createElement('button');
                    optionBtn.type = 'button';
                    optionBtn.className = 'map-picker-option';
                    optionBtn.dataset.mapId = String(data.id);
                    optionBtn.dataset.kokapena = data.kokapena || '';
                    optionBtn.dataset.imageUrl = data.image_url || '';
                    optionBtn.dataset.label = `${data.name} (ID ${data.id})`;
                    optionBtn.setAttribute('role', 'option');
                    optionBtn.setAttribute('aria-selected', 'true');

                    if (data.image_url) {
                        const img = document.createElement('img');
                        img.src = data.image_url;
                        img.alt = data.name;
                        optionBtn.appendChild(img);
                    } else {
                        const placeholder = document.createElement('div');
                        placeholder.className = 'map-picker-thumb map-picker-placeholder';
                        placeholder.setAttribute('aria-hidden', 'true');
                        placeholder.textContent = '?';
                        optionBtn.appendChild(placeholder);
                    }

                    const label = document.createElement('span');
                    label.textContent = `${data.name} (ID ${data.id})`;
                    optionBtn.appendChild(label);

                    mapPickerList.appendChild(optionBtn);
                    bindMapPickerOptions(mapPickerList);
                    updateMapPicker(optionBtn.dataset.label, optionBtn.dataset.imageUrl);
                }

                closeMapModal();
                filterMapsByLocation();
            } catch (error) {
                const isTimeout = error?.name === 'AbortError';
                const detail = error instanceof Error ? ` (${error.message})` : '';
                const message = isTimeout
                    ? 'Denbora agortu da mapa gordetzean. Saiatu berriro.'
                    : `Sareko errorea mapa gordetzean${detail}.`;
                mapError.textContent = message;
                mapError.classList.remove('hidden-error');
                showSnackbar(message);
            } finally {
                window.setButtonLoading?.(saveMapBtn, false);
            }
        });

        locationForm?.addEventListener('submit', async (event) => {
            event.preventDefault();
            locationError.classList.add('hidden-error');
            locationError.textContent = '';
            window.setButtonLoading?.(saveLocationBtn, true);

            try {
                const payload = new FormData();
                payload.append('_token', locationForm.querySelector('input[name="_token"]').value);
                payload.append('name', locationForm.querySelector('#location-name').value);

                const requestOptions = {
                    method: 'POST',
                    body: payload,
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': locationForm.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                };

                const response = window.appFetch
                    ? await window.appFetch('{{ route('kilter.locations.store') }}', { ...requestOptions, timeoutMs: 12000, showError: false })
                    : await fetch('{{ route('kilter.locations.store') }}', requestOptions);
                const data = await response.json();

                if (!response.ok) {
                    let msg = data?.message || `Errorea ${response.status} kokapena gordetzean.`;
                    if (data?.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        const firstError = firstKey ? data.errors[firstKey]?.[0] : null;
                        if (firstError) msg = firstError;
                    }
                    locationError.textContent = msg;
                    locationError.classList.remove('hidden-error');
                    showSnackbar(msg);
                    return;
                }

                const option = document.createElement('option');
                option.value = data.name;
                option.textContent = data.name;
                locationSelect.appendChild(option);
                locationSelect.value = data.name;
                closeLocationModal();
                filterMapsByLocation();
            } catch (error) {
                const detail = error instanceof Error ? ` (${error.message})` : '';
                const message = `Sareko errorea kokapena gordetzean${detail}.`;
                locationError.textContent = message;
                locationError.classList.remove('hidden-error');
                showSnackbar(message);
            } finally {
                window.setButtonLoading?.(saveLocationBtn, false);
            }
        });

        bindMapPickerOptions(mapPickerList);
        syncMapPickerFromSelect();
        filterMapsByLocation();

        // Modal de coordenadas
        const boulderModal = document.getElementById('boulder-modal');
        const closeBoulderBtn = document.getElementById('close-boulder-modal');
        const cancelBoulderBtn = document.getElementById('cancel-boulder-modal');
        const saveBoulderBtn = document.getElementById('save-boulder-points');
        const boulderError = document.getElementById('boulder-modal-error');
        const coordImage = document.getElementById('coord-image');
        const coordLayer = document.getElementById('coord-layer');
        const coordStage = document.getElementById('coord-stage');
        const coordCanvasWrap = document.getElementById('coord-canvas-wrap');
        const coordModeSelect = document.getElementById('coord-mode');
        const pointTypeSelect = document.getElementById('point-type');
        const pointSizeSelect = document.getElementById('point-size');
        const pointTypeWrap = document.getElementById('point-type-wrap');
        const pointSizeWrap = document.getElementById('point-size-wrap');

        const clearPointsBtn = document.getElementById('clear-points');

        let tempMode = 'points';
        let tempPoints = [];
        let zoom = 1;
        let isPanning = false;
        let panStartX = 0;
        let panStartY = 0;
        let panStartScrollLeft = 0;
        let panStartScrollTop = 0;
        let panDistance = 0;
        let suppressImageClick = false;
        const activeTouchPoints = new Map();
        let pinchStartDistance = 0;
        let pinchStartZoom = 1;
        let baseImageWidth = 0;
        let minZoom = 0.01;

        function hasFinePointer() {
            return window.matchMedia('(pointer: fine)').matches;
        }

        function distanceBetweenTouches() {
            const points = Array.from(activeTouchPoints.values());
            if (points.length < 2) return 0;
            const dx = points[0].x - points[1].x;
            const dy = points[0].y - points[1].y;
            return Math.hypot(dx, dy);
        }

        function setZoom(value) {
            zoom = Math.min(3, Math.max(minZoom, value));
            if (!coordImage) return;
            if (!baseImageWidth) {
                const fallbackWidth = coordCanvasWrap?.clientWidth || 680;
                baseImageWidth = Math.max(680, Math.round(fallbackWidth));
            }
            coordImage.style.width = `${Math.round(baseImageWidth * zoom)}px`;
            coordLayer.style.setProperty('--point-scale', String(zoom));
            requestAnimationFrame(renderPoints);
        }

        function fitImageZoom() {
            const wrapWidth = coordCanvasWrap?.clientWidth || 0;
            const wrapHeight = coordCanvasWrap?.clientHeight || 0;
            const naturalWidth = coordImage?.naturalWidth || 0;
            const naturalHeight = coordImage?.naturalHeight || 0;
            if (!wrapWidth || !wrapHeight || !naturalWidth || !naturalHeight) {
                minZoom = 0.01;
                return 1;
            }

            if (!baseImageWidth) {
                baseImageWidth = Math.max(680, Math.round(wrapWidth));
            }

            const imageHeightAtZoom1 = baseImageWidth * (naturalHeight / naturalWidth);
            const fitByWidth = wrapWidth / baseImageWidth;
            const fitByHeight = wrapHeight / imageHeightAtZoom1;
            const fitZoom = Math.min(fitByWidth, fitByHeight);

            minZoom = Math.max(0.001, fitZoom * 0.35);
            return Math.min(1, fitZoom);
        }

        function drawLine(from, to, className) {
            const layerWidth = coordLayer.clientWidth || coordImage.clientWidth || 0;
            const layerHeight = coordLayer.clientHeight || coordImage.clientHeight || 0;
            if (!layerWidth || !layerHeight) return null;

            const x1 = (from.x / 100) * layerWidth;
            const y1 = (from.y / 100) * layerHeight;
            const x2 = (to.x / 100) * layerWidth;
            const y2 = (to.y / 100) * layerHeight;
            const dx = x2 - x1;
            const dy = y2 - y1;
            const lengthPx = Math.hypot(dx, dy);
            const angleDeg = Math.atan2(dy, dx) * (180 / Math.PI);
            const segment = document.createElement('span');
            segment.className = className;
            segment.style.left = `${x1}px`;
            segment.style.top = `${y1}px`;
            segment.style.width = `${lengthPx}px`;
            segment.style.transform = `rotate(${angleDeg}deg)`;
            return segment;
        }

        function syncModeUi() {
            const isLineMode = tempMode === 'line';
            if (pointTypeWrap) {
                pointTypeWrap.classList.toggle('is-hidden', isLineMode);
                pointTypeWrap.setAttribute('aria-hidden', isLineMode ? 'true' : 'false');
            }
            if (pointSizeWrap) {
                pointSizeWrap.classList.toggle('is-hidden', isLineMode);
                pointSizeWrap.setAttribute('aria-hidden', isLineMode ? 'true' : 'false');
            }
        }

        function renderPoints() {
            coordLayer.innerHTML = '';

            if (tempMode === 'line') {
                for (let i = 0; i < tempPoints.length - 1; i += 1) {
                    const segment = drawLine(tempPoints[i], tempPoints[i + 1], 'coord-line');
                    if (segment) coordLayer.appendChild(segment);
                }
            }

            tempPoints.forEach((point, index) => {
                const marker = document.createElement('button');
                marker.type = 'button';
                if (tempMode === 'line') {
                    marker.className = 'coord-point line-node';
                } else {
                    const type = point.type || 'mano_pie';
                    const size = point.size || 'mediano';
                    const isTraveNumbered = tempMode === 'trave' && type === 'mano_pie';
                    marker.className = `coord-point type-${type} size-${size}${isTraveNumbered ? ' is-numbered' : ''}`;
                    if (isTraveNumbered) {
                        const label = document.createElement('span');
                        label.className = 'coord-point-number';
                        label.textContent = String(point.order);
                        marker.appendChild(label);
                    }
                }
                marker.style.left = `${point.x}%`;
                marker.style.top = `${point.y}%`;
                marker.title = `Puntua ${index + 1} (klik ezabatzeko)`;
                marker.addEventListener('click', (event) => {
                    event.stopPropagation();
                    tempPoints.splice(index, 1);
                    if (tempMode === 'trave') {
                        tempPoints = applyTraveOrder(tempPoints);
                    }
                    renderPoints();
                });
                coordLayer.appendChild(marker);
            });
        }

        function openBoulderModal() {
            const selected = mapSelect.options[mapSelect.selectedIndex];
            const imageUrl = selected?.dataset?.imageUrl || '';

            if (!mapSelect.value) {
                boulderError.textContent = 'Lehenengo mapa bat hautatu.';
                boulderError.classList.remove('hidden-error');
                return;
            }

            if (!imageUrl) {
                boulderError.textContent = 'Hautatutako mapak ez du irudi erabilgarririk.';
                boulderError.classList.remove('hidden-error');
                return;
            }

            boulderError.classList.add('hidden-error');
            boulderError.textContent = '';
            coordImage.src = imageUrl;
            const state = parseBoulderState();
            tempMode = state.mode;
            tempPoints = [...state.points];
            if (coordModeSelect) coordModeSelect.value = tempMode;
            minZoom = 0.001;
            setZoom(1);
            if (coordCanvasWrap) {
                coordCanvasWrap.scrollLeft = 0;
                coordCanvasWrap.scrollTop = 0;
            }
            activeTouchPoints.clear();
            isPanning = false;
            pinchStartDistance = 0;
            pinchStartZoom = zoom;
            baseImageWidth = 0;
            coordImage.style.cursor = hasFinePointer() ? 'grab' : 'crosshair';
            syncModeUi();
            renderPoints();
            boulderModal.classList.remove('hidden-modal');
            syncBodyScrollLock();
        }

        function closeBoulderModal() {
            boulderModal.classList.add('hidden-modal');
            boulderError.classList.add('hidden-error');
            boulderError.textContent = '';
            activeTouchPoints.clear();
            isPanning = false;
            syncBodyScrollLock();
        }

        openBoulderBtn?.addEventListener('click', openBoulderModal);
        closeBoulderBtn?.addEventListener('click', closeBoulderModal);
        cancelBoulderBtn?.addEventListener('click', closeBoulderModal);
        boulderModal?.addEventListener('click', (event) => {
            if (event.target === boulderModal) closeBoulderModal();
        });

        coordImage?.addEventListener('click', (event) => {
            if (suppressImageClick) {
                suppressImageClick = false;
                return;
            }
            if (!coordImage.src) return;
            const rect = coordImage.getBoundingClientRect();
            if (!rect.width || !rect.height) return;

            const x = ((event.clientX - rect.left) / rect.width) * 100;
            const y = ((event.clientY - rect.top) / rect.height) * 100;

            if (tempMode === 'line') {
                tempPoints.push({
                    x: Number(x.toFixed(3)),
                    y: Number(y.toFixed(3)),
                });
            } else {
                const type = pointTypeSelect?.value || 'mano_pie';
                const size = pointSizeSelect?.value || 'mediano';

                tempPoints.push({
                    x: Number(x.toFixed(3)),
                    y: Number(y.toFixed(3)),
                    type,
                    size,
                });
            }

            if (tempMode === 'trave') {
                tempPoints = applyTraveOrder(tempPoints);
            }

            renderPoints();
        });

        coordImage?.addEventListener('mousedown', (event) => {
            if (!hasFinePointer()) return;
            if (event.button !== 0) return;
            if (!coordCanvasWrap) return;

            isPanning = true;
            panDistance = 0;
            panStartX = event.clientX;
            panStartY = event.clientY;
            panStartScrollLeft = coordCanvasWrap.scrollLeft;
            panStartScrollTop = coordCanvasWrap.scrollTop;
            coordImage.style.cursor = 'grabbing';
            event.preventDefault();
        });

        window.addEventListener('mousemove', (event) => {
            if (!isPanning || !coordCanvasWrap) return;

            const deltaX = event.clientX - panStartX;
            const deltaY = event.clientY - panStartY;
            panDistance = Math.max(panDistance, Math.abs(deltaX) + Math.abs(deltaY));

            coordCanvasWrap.scrollLeft = panStartScrollLeft - deltaX;
            coordCanvasWrap.scrollTop = panStartScrollTop - deltaY;
        });

        window.addEventListener('mouseup', () => {
            if (!isPanning) return;
            isPanning = false;
            coordImage.style.cursor = hasFinePointer() ? 'grab' : 'crosshair';

            if (panDistance > 3) {
                suppressImageClick = true;
                setTimeout(() => {
                    suppressImageClick = false;
                }, 0);
            }
        });

        coordCanvasWrap?.addEventListener('wheel', (event) => {
            if (!hasFinePointer()) return;
            event.preventDefault();
            const step = event.deltaY < 0 ? 0.12 : -0.12;
            setZoom(zoom + step);
        }, { passive: false });

        coordImage?.addEventListener('load', () => {
            const wrapWidth = coordCanvasWrap?.clientWidth || 680;
            baseImageWidth = Math.max(680, Math.round(wrapWidth));
            const fitZoom = fitImageZoom();
            setZoom(fitZoom);
            if (coordCanvasWrap) {
                coordCanvasWrap.scrollLeft = 0;
                coordCanvasWrap.scrollTop = 0;
            }
        });

        coordCanvasWrap?.addEventListener('touchstart', (event) => {
            if (!coordCanvasWrap) return;

            for (const touch of event.changedTouches) {
                activeTouchPoints.set(touch.identifier, { x: touch.clientX, y: touch.clientY });
            }

            if (activeTouchPoints.size === 1) {
                const point = Array.from(activeTouchPoints.values())[0];
                isPanning = true;
                panDistance = 0;
                panStartX = point.x;
                panStartY = point.y;
                panStartScrollLeft = coordCanvasWrap.scrollLeft;
                panStartScrollTop = coordCanvasWrap.scrollTop;
            } else if (activeTouchPoints.size >= 2) {
                isPanning = false;
                pinchStartDistance = distanceBetweenTouches();
                pinchStartZoom = zoom;
            }
        }, { passive: true });

        coordCanvasWrap?.addEventListener('touchmove', (event) => {
            if (!coordCanvasWrap) return;

            for (const touch of event.changedTouches) {
                if (activeTouchPoints.has(touch.identifier)) {
                    activeTouchPoints.set(touch.identifier, { x: touch.clientX, y: touch.clientY });
                }
            }

            if (activeTouchPoints.size >= 2) {
                const nextDistance = distanceBetweenTouches();
                if (pinchStartDistance > 0 && nextDistance > 0) {
                    const scaleFactor = nextDistance / pinchStartDistance;
                    setZoom(pinchStartZoom * scaleFactor);
                    suppressImageClick = true;
                }
                event.preventDefault();
                return;
            }

            if (!isPanning) return;
            const point = Array.from(activeTouchPoints.values())[0];
            if (!point) return;

            const deltaX = point.x - panStartX;
            const deltaY = point.y - panStartY;
            panDistance = Math.max(panDistance, Math.abs(deltaX) + Math.abs(deltaY));
            coordCanvasWrap.scrollLeft = panStartScrollLeft - deltaX;
            coordCanvasWrap.scrollTop = panStartScrollTop - deltaY;

            if (panDistance > 3) {
                suppressImageClick = true;
            }
            event.preventDefault();
        }, { passive: false });

        coordCanvasWrap?.addEventListener('touchend', (event) => {
            for (const touch of event.changedTouches) {
                activeTouchPoints.delete(touch.identifier);
            }

            if (activeTouchPoints.size === 1) {
                const point = Array.from(activeTouchPoints.values())[0];
                isPanning = true;
                panDistance = 0;
                panStartX = point.x;
                panStartY = point.y;
                panStartScrollLeft = coordCanvasWrap?.scrollLeft ?? 0;
                panStartScrollTop = coordCanvasWrap?.scrollTop ?? 0;
                pinchStartDistance = 0;
                pinchStartZoom = zoom;
                return;
            }

            isPanning = false;
            pinchStartDistance = 0;
            pinchStartZoom = zoom;
        }, { passive: true });

        coordCanvasWrap?.addEventListener('touchcancel', (event) => {
            for (const touch of event.changedTouches) {
                activeTouchPoints.delete(touch.identifier);
            }
            isPanning = false;
            pinchStartDistance = 0;
            pinchStartZoom = zoom;
        }, { passive: true });

        clearPointsBtn?.addEventListener('click', () => {
            tempPoints = [];
            renderPoints();
        });

        coordModeSelect?.addEventListener('change', () => {
            const nextMode = validModes.includes(coordModeSelect.value) ? coordModeSelect.value : 'points';
            tempMode = nextMode;
            tempPoints = normalizePointCollection(tempPoints, tempMode);
            syncModeUi();
            renderPoints();
        });

        saveBoulderBtn?.addEventListener('click', () => {
            boulderInput.value = JSON.stringify({
                mode: tempMode,
                points: tempMode === 'trave' ? applyTraveOrder(tempPoints) : tempPoints,
            });
            syncBoulderButtonState();
            closeBoulderModal();
        });
    })();
</script>
@endsection
