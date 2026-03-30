@extends('layouts.app', ['title' => 'Blokea editatu | KILTER'])

@section('content')
@php
    $initialBoulder = old('boulder', $block->boulder);
@endphp
<section class="kilter-page">
    <div class="kilter-form-wrap">
        <p class="eyebrow">Kilter Board Hub</p>
        <h1>BLOKEA EDITATU</h1>

        <form method="POST" action="{{ route('kilter.update', $block) }}" class="kilter-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label>Blokearen izena</label>
            <input type="text" name="name" value="{{ old('name', $block->name) }}" required>
            @error('name')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Deskribapena</label>
            <textarea name="description" rows="4" required>{{ old('description', $block->description) }}</textarea>
            @error('description')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Gradua</label>
            <select name="grade" required>
                <option value="">Hautatu gradua</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade }}" @selected(old('grade', $block->grade) === $grade)>{{ $grade }}</option>
                @endforeach
            </select>
            @error('grade')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Mapa</label>
            <div class="map-picker-row">
                <select name="map_id" id="map-select" required>
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
                            data-image-url="{{ $imageUrl }}"
                            @selected((string) old('map_id', $block->map_id) === (string) $map->id)
                        >
                            {{ $map->name }} (ID {{ $map->id }})
                        </option>
                    @endforeach
                </select>

                <button type="button" class="btn btn-secondary map-upload-toggle" id="open-map-modal">
                    <span class="map-upload-toggle-label">Mapa gehitu</span>
                </button>
            </div>
            @error('map_id')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Boulder koordenatuak</label>
            <input type="hidden" name="boulder" id="boulder-input" value="{{ $initialBoulder }}">
            <div class="boulder-row">
                <button type="button" class="btn btn-secondary" id="open-boulder-modal">Koordenatuak zehaztu</button>
                <span id="boulder-summary" class="boulder-summary">Ez dago punturik zehaztuta</span>
            </div>
            @error('boulder')
                <small class="error">{{ $message }}</small>
            @enderror

            <div class="kilter-form-actions">
                <button type="submit" class="btn btn-primary">Aldaketak gorde</button>
                <a href="{{ route('kilter') }}" class="btn btn-secondary">Utzi</a>
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
                <button type="submit" class="btn btn-primary" id="save-map-btn">Mapa gorde</button>
                <button type="button" class="btn btn-secondary" id="cancel-map-modal">Utzi</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-shell hidden-modal" id="boulder-modal" role="dialog" aria-modal="true" aria-labelledby="boulder-modal-title">
    <div class="modal-card modal-card-xl">
        <div class="modal-head">
            <h2 id="boulder-modal-title">Boulder koordenatuak hautatu</h2>
            <button type="button" class="icon-btn" id="close-boulder-modal" aria-label="Itxi leihoa">×</button>
        </div>

        <div class="coord-toolbar">
            <label for="coord-mode">Marrazki mota</label>
            <select id="coord-mode">
                <option value="points" selected>Zirkuluak</option>
                <option value="line">Lerroa</option>
            </select>
            <button type="button" class="btn btn-secondary" id="clear-points">Puntuak garbitu</button>
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
            <span id="coord-count">0 puntu</span>
        </div>

        <div class="coord-canvas-wrap" id="coord-canvas-wrap">
            <div class="coord-stage" id="coord-stage">
                <img id="coord-image" alt="Koordenatuetarako mapa">
                <div id="coord-layer"></div>
            </div>
        </div>

        <small class="error hidden-error" id="boulder-modal-error"></small>

        <div class="kilter-form-actions">
            <button type="button" class="btn btn-primary" id="save-boulder-points">Koordenatuak gorde</button>
            <button type="button" class="btn btn-secondary" id="cancel-boulder-modal">Utzi</button>
        </div>
    </div>
</div>

<div id="app-snackbar" class="app-snackbar" role="status" aria-live="polite"></div>

<script>
    (function () {
        const mapSelect = document.getElementById('map-select');
        const boulderInput = document.getElementById('boulder-input');
        const boulderSummary = document.getElementById('boulder-summary');
        const validModes = ['points', 'line'];
        const validTypes = ['pie', 'mano_pie', 'comienzo', 'top'];
        const validSizes = ['pequeno', 'mediano', 'grande', 'gigante'];

        function normalizeBoulderState(payload) {
            if (Array.isArray(payload)) {
                return {
                    mode: 'points',
                    points: payload.map((point) => ({
                        x: Number(point?.x ?? 0),
                        y: Number(point?.y ?? 0),
                        type: validTypes.includes(point?.type) ? point.type : 'mano_pie',
                        size: validSizes.includes(point?.size) ? point.size : 'mediano',
                    })),
                };
            }

            if (payload && typeof payload === 'object' && Array.isArray(payload.points)) {
                const mode = validModes.includes(payload.mode) ? payload.mode : 'points';
                if (mode === 'line') {
                    return {
                        mode,
                        points: payload.points.map((point) => ({
                            x: Number(point?.x ?? 0),
                            y: Number(point?.y ?? 0),
                        })),
                    };
                }

                return {
                    mode: 'points',
                    points: payload.points.map((point) => ({
                        x: Number(point?.x ?? 0),
                        y: Number(point?.y ?? 0),
                        type: validTypes.includes(point?.type) ? point.type : 'mano_pie',
                        size: validSizes.includes(point?.size) ? point.size : 'mediano',
                    })),
                };
            }

            return { mode: 'points', points: [] };
        }

        function parseBoulderState() {
            try {
                const parsed = JSON.parse(boulderInput.value || '[]');
                return normalizeBoulderState(parsed);
            } catch {
                return { mode: 'points', points: [] };
            }
        }

        function syncBoulderSummary() {
            const state = parseBoulderState();
            const points = state.points;
            if (points.length === 0) {
                boulderSummary.textContent = 'Ez dago punturik zehaztuta';
                return;
            }

            if (state.mode === 'line') {
                boulderSummary.textContent = `${points.length} puntu lotuta (lerroa)`;
                return;
            }

            boulderSummary.textContent = points.length > 0
                ? `${points.length} puntu zehaztuta (zirkuluak)`
                : 'Ez dago punturik zehaztuta';
        }

        function syncBodyScrollLock() {
            const hasOpenModal = document.querySelector('.modal-shell:not(.hidden-modal)') !== null;
            document.body.style.overflow = hasOpenModal ? 'hidden' : '';
        }

        syncBoulderSummary();

        // Modal de mapa
        const mapModal = document.getElementById('map-modal');
        const openMapBtn = document.getElementById('open-map-modal');
        const closeMapBtn = document.getElementById('close-map-modal');
        const cancelMapBtn = document.getElementById('cancel-map-modal');
        const mapForm = document.getElementById('map-create-form');
        const mapError = document.getElementById('map-modal-error');
        const saveMapBtn = document.getElementById('save-map-btn');
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

        openMapBtn?.addEventListener('click', openMapModal);
        closeMapBtn?.addEventListener('click', closeMapModal);
        cancelMapBtn?.addEventListener('click', closeMapModal);
        mapModal?.addEventListener('click', (event) => {
            if (event.target === mapModal) closeMapModal();
        });

        mapForm?.addEventListener('submit', async (event) => {
            event.preventDefault();

            mapError.classList.add('hidden-error');
            mapError.textContent = '';
            saveMapBtn.disabled = true;

            const payload = new FormData();
            payload.append('_token', mapForm.querySelector('input[name="_token"]').value);
            payload.append('name', mapForm.querySelector('#map-name').value);

            const file = mapFileInput?.files?.[0];
            const chosenFile = file;

            if (!chosenFile) {
                mapError.textContent = 'Irudi bat hautatu behar duzu.';
                mapError.classList.remove('hidden-error');
                showSnackbar('Irudi bat hautatu behar duzu.');
                saveMapBtn.disabled = false;
                return;
            }

            if (chosenFile.size > maxMapFileBytes) {
                mapError.textContent = 'Irudia handiegia da. Gehienez 20MB onartzen dira.';
                mapError.classList.remove('hidden-error');
                showSnackbar('Irudia handiegia da. Gehienez 20MB onartzen dira.');
                saveMapBtn.disabled = false;
                return;
            }

            payload.append('image', chosenFile);

            try {
                const response = await fetch('{{ route('kilter.maps.store') }}', {
                    method: 'POST',
                    body: payload,
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': mapForm.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

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
                    saveMapBtn.disabled = false;
                    return;
                }

                const option = document.createElement('option');
                option.value = data.id;
                option.textContent = `${data.name} (ID ${data.id})`;
                option.dataset.imageUrl = data.image_url || '';
                option.selected = true;
                mapSelect.appendChild(option);
                mapSelect.value = String(data.id);

                closeMapModal();
            } catch (error) {
                const detail = error instanceof Error ? ` (${error.message})` : '';
                mapError.textContent = `Sareko errorea mapa gordetzean${detail}.`;
                mapError.classList.remove('hidden-error');
                showSnackbar(`Sareko errorea mapa gordetzean${detail}.`);
            } finally {
                saveMapBtn.disabled = false;
            }
        });

        // Modal de coordenadas
        const boulderModal = document.getElementById('boulder-modal');
        const openBoulderBtn = document.getElementById('open-boulder-modal');
        const closeBoulderBtn = document.getElementById('close-boulder-modal');
        const cancelBoulderBtn = document.getElementById('cancel-boulder-modal');
        const saveBoulderBtn = document.getElementById('save-boulder-points');
        const boulderError = document.getElementById('boulder-modal-error');
        const coordImage = document.getElementById('coord-image');
        const coordLayer = document.getElementById('coord-layer');
        const coordStage = document.getElementById('coord-stage');
        const coordCanvasWrap = document.getElementById('coord-canvas-wrap');
        const coordCount = document.getElementById('coord-count');
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
                    marker.className = `coord-point type-${type} size-${size}`;
                }
                marker.style.left = `${point.x}%`;
                marker.style.top = `${point.y}%`;
                marker.title = `Puntua ${index + 1} (klik ezabatzeko)`;
                marker.addEventListener('click', (event) => {
                    event.stopPropagation();
                    tempPoints.splice(index, 1);
                    renderPoints();
                });
                coordLayer.appendChild(marker);
            });
            coordCount.textContent = tempMode === 'line'
                ? `${tempPoints.length} puntu (lerroa)`
                : `${tempPoints.length} puntu (zirkuluak)`;
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
            tempPoints = state.points;
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
            const nextMode = coordModeSelect.value === 'line' ? 'line' : 'points';
            tempMode = nextMode;
            if (tempMode === 'points') {
                tempPoints = tempPoints.map((point) => ({
                    x: Number(point?.x ?? 0),
                    y: Number(point?.y ?? 0),
                    type: validTypes.includes(point?.type) ? point.type : 'mano_pie',
                    size: validSizes.includes(point?.size) ? point.size : 'mediano',
                }));
            } else {
                tempPoints = tempPoints.map((point) => ({
                    x: Number(point?.x ?? 0),
                    y: Number(point?.y ?? 0),
                }));
            }
            syncModeUi();
            renderPoints();
        });

        saveBoulderBtn?.addEventListener('click', () => {
            boulderInput.value = JSON.stringify({
                mode: tempMode,
                points: tempPoints,
            });
            syncBoulderSummary();
            closeBoulderModal();
        });
    })();
</script>
@endsection
