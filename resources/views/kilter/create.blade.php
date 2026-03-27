@extends('layouts.app', ['title' => 'Crear bloque | KILTER'])

@section('content')
<section class="kilter-page">
    <div class="kilter-form-wrap">
        <p class="eyebrow">Kilter Board Hub</p>
        <h1>CREAR BLOQUE</h1>

        <form method="POST" action="{{ route('kilter.store') }}" class="kilter-form" enctype="multipart/form-data">
            @csrf

            <label>Nombre del bloque</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Descripción</label>
            <textarea name="description" rows="4" required>{{ old('description') }}</textarea>
            @error('description')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Grado</label>
            <select name="grade" required>
                <option value="">Selecciona grado</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade }}" @selected(old('grade') === $grade)>{{ $grade }}</option>
                @endforeach
            </select>
            @error('grade')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Mapa</label>
            <div class="map-picker-row">
                <select name="map_id" id="map-select" required>
                    <option value="">Selecciona un mapa</option>
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
                            @selected((string) old('map_id') === (string) $map->id)
                        >
                            {{ $map->name }} (ID {{ $map->id }})
                        </option>
                    @endforeach
                </select>

                <button type="button" class="btn btn-secondary map-upload-toggle" id="open-map-modal">
                    <span class="map-upload-toggle-label">Añadir mapa</span>
                </button>
            </div>
            @error('map_id')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Coordenadas boulder</label>
            <input type="hidden" name="boulder" id="boulder-input" value="{{ old('boulder') }}">
            <div class="boulder-row">
                <button type="button" class="btn btn-secondary" id="open-boulder-modal">Definir coordenadas</button>
                <span id="boulder-summary" class="boulder-summary">Sin puntos definidos</span>
            </div>
            @error('boulder')
                <small class="error">{{ $message }}</small>
            @enderror

            <div class="kilter-form-actions">
                <button type="submit" class="btn btn-primary">Guardar bloque</button>
                <a href="{{ route('kilter') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</section>

<div class="modal-shell hidden-modal" id="map-modal" role="dialog" aria-modal="true" aria-labelledby="map-modal-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="map-modal-title">Añadir mapa</h2>
            <button type="button" class="icon-btn" id="close-map-modal" aria-label="Cerrar modal">×</button>
        </div>

        <form id="map-create-form" class="kilter-form" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <label>Nombre del mapa</label>
            <input type="text" name="name" id="map-name" required>

            <label class="map-upload-label">Seleccionar imagen</label>
            <input type="file" name="image" id="map-image-file" accept="image/*" required>

            @if($isMobileClient)
                <label class="map-upload-label">O sacar foto en este momento</label>
                <input type="file" name="image_camera" id="map-image-camera" accept="image/*" capture="environment">
            @endif

            <small class="error hidden-error" id="map-modal-error"></small>

            <div class="kilter-form-actions">
                <button type="submit" class="btn btn-primary" id="save-map-btn">Guardar mapa</button>
                <button type="button" class="btn btn-secondary" id="cancel-map-modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-shell hidden-modal" id="boulder-modal" role="dialog" aria-modal="true" aria-labelledby="boulder-modal-title">
    <div class="modal-card modal-card-xl">
        <div class="modal-head">
            <h2 id="boulder-modal-title">Seleccionar coordenadas boulder</h2>
            <button type="button" class="icon-btn" id="close-boulder-modal" aria-label="Cerrar modal">×</button>
        </div>

        <div class="coord-toolbar">
            <button type="button" class="btn btn-secondary" id="zoom-out">- Zoom</button>
            <button type="button" class="btn btn-secondary" id="zoom-in">+ Zoom</button>
            <button type="button" class="btn btn-secondary" id="zoom-reset">Reset</button>
            <button type="button" class="btn btn-secondary" id="clear-points">Limpiar puntos</button>
            <label for="point-type">Tipo</label>
            <select id="point-type">
                <option value="pie">Amarillo - pie</option>
                <option value="mano_pie" selected>Azul - mano/pie</option>
                <option value="comienzo">Rosa - comienzo</option>
                <option value="top">Rojo - top</option>
            </select>
            <label for="point-size">Tamaño</label>
            <select id="point-size">
                <option value="pequeno">Pequeño</option>
                <option value="mediano" selected>Mediano</option>
                <option value="grande">Grande</option>
                <option value="gigante">Gigante</option>
            </select>
            <span id="coord-count">0 puntos</span>
        </div>

        <div class="coord-canvas-wrap" id="coord-canvas-wrap">
            <div class="coord-stage" id="coord-stage">
                <img id="coord-image" alt="Mapa para coordenadas">
                <div id="coord-layer"></div>
            </div>
        </div>

        <small class="error hidden-error" id="boulder-modal-error"></small>

        <div class="kilter-form-actions">
            <button type="button" class="btn btn-primary" id="save-boulder-points">Guardar coordenadas</button>
            <button type="button" class="btn btn-secondary" id="cancel-boulder-modal">Cancelar</button>
        </div>
    </div>
</div>

<script>
    (function () {
        const mapSelect = document.getElementById('map-select');
        const boulderInput = document.getElementById('boulder-input');
        const boulderSummary = document.getElementById('boulder-summary');

        function parsePoints() {
            try {
                const parsed = JSON.parse(boulderInput.value || '[]');
                return Array.isArray(parsed) ? parsed : [];
            } catch {
                return [];
            }
        }

        function syncBoulderSummary() {
            const points = parsePoints();
            boulderSummary.textContent = points.length > 0
                ? `${points.length} punto(s) definido(s)`
                : 'Sin puntos definidos';
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
        const mapCameraInput = document.getElementById('map-image-camera');
        const mapFileInput = document.getElementById('map-image-file');

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

        mapCameraInput?.addEventListener('change', () => {
            if (mapCameraInput.files && mapCameraInput.files.length > 0) {
                mapFileInput.value = '';
            }
        });

        mapFileInput?.addEventListener('change', () => {
            if (mapFileInput.files && mapFileInput.files.length > 0 && mapCameraInput) {
                mapCameraInput.value = '';
            }
        });

        mapForm?.addEventListener('submit', async (event) => {
            event.preventDefault();

            mapError.classList.add('hidden-error');
            mapError.textContent = '';
            saveMapBtn.disabled = true;

            const payload = new FormData();
            payload.append('_token', mapForm.querySelector('input[name="_token"]').value);
            payload.append('name', mapForm.querySelector('#map-name').value);

            const cameraFile = mapCameraInput?.files?.[0];
            const file = mapFileInput?.files?.[0];
            const chosenFile = cameraFile || file;

            if (!chosenFile) {
                mapError.textContent = 'Debes seleccionar una imagen o sacar una foto.';
                mapError.classList.remove('hidden-error');
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
                    let msg = data?.message || `Error ${response.status} guardando el mapa.`;
                    if (data?.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        const firstError = firstKey ? data.errors[firstKey]?.[0] : null;
                        if (firstError) msg = firstError;
                    }
                    if (response.status === 419) {
                        msg = 'Sesión caducada (419). Recarga la página e inténtalo de nuevo.';
                    } else if (response.status === 413) {
                        msg = 'La imagen es demasiado grande para el servidor (413).';
                    } else if (!data && rawText) {
                        msg = `Error ${response.status}. Revisa logs del servidor.`;
                    }
                    mapError.textContent = msg;
                    mapError.classList.remove('hidden-error');
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
                mapError.textContent = `Error de red guardando el mapa${detail}.`;
                mapError.classList.remove('hidden-error');
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
        const pointTypeSelect = document.getElementById('point-type');
        const pointSizeSelect = document.getElementById('point-size');

        const zoomOutBtn = document.getElementById('zoom-out');
        const zoomInBtn = document.getElementById('zoom-in');
        const zoomResetBtn = document.getElementById('zoom-reset');
        const clearPointsBtn = document.getElementById('clear-points');

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
            zoom = Math.min(3, Math.max(0.5, value));
            if (!coordImage) return;
            if (!baseImageWidth) {
                const fallbackWidth = coordCanvasWrap?.clientWidth || 680;
                baseImageWidth = Math.max(680, Math.round(fallbackWidth));
            }
            coordImage.style.width = `${Math.round(baseImageWidth * zoom)}px`;
        }

        function renderPoints() {
            coordLayer.innerHTML = '';
            tempPoints.forEach((point, index) => {
                const marker = document.createElement('button');
                marker.type = 'button';
                const type = point.type || 'mano_pie';
                const size = point.size || 'mediano';
                marker.className = `coord-point type-${type} size-${size}`;
                marker.style.left = `${point.x}%`;
                marker.style.top = `${point.y}%`;
                marker.title = `Punto ${index + 1} (click para borrar)`;
                marker.addEventListener('click', (event) => {
                    event.stopPropagation();
                    tempPoints.splice(index, 1);
                    renderPoints();
                });
                coordLayer.appendChild(marker);
            });
            coordCount.textContent = `${tempPoints.length} punto(s)`;
        }

        function openBoulderModal() {
            const selected = mapSelect.options[mapSelect.selectedIndex];
            const imageUrl = selected?.dataset?.imageUrl || '';

            if (!mapSelect.value) {
                boulderError.textContent = 'Selecciona primero un mapa.';
                boulderError.classList.remove('hidden-error');
                return;
            }

            if (!imageUrl) {
                boulderError.textContent = 'El mapa seleccionado no tiene imagen disponible.';
                boulderError.classList.remove('hidden-error');
                return;
            }

            boulderError.classList.add('hidden-error');
            boulderError.textContent = '';
            coordImage.src = imageUrl;
            tempPoints = parsePoints().map((point) => ({
                x: Number(point?.x ?? 0),
                y: Number(point?.y ?? 0),
                type: point?.type || 'mano_pie',
                size: point?.size || 'mediano',
            }));
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

            const type = pointTypeSelect?.value || 'mano_pie';
            const size = pointSizeSelect?.value || 'mediano';

            tempPoints.push({
                x: Number(x.toFixed(3)),
                y: Number(y.toFixed(3)),
                type,
                size,
            });

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
            setZoom(zoom);
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

        zoomOutBtn?.addEventListener('click', () => setZoom(zoom - 0.12));
        zoomInBtn?.addEventListener('click', () => setZoom(zoom + 0.12));
        zoomResetBtn?.addEventListener('click', () => setZoom(1));
        clearPointsBtn?.addEventListener('click', () => {
            tempPoints = [];
            renderPoints();
        });

        saveBoulderBtn?.addEventListener('click', () => {
            boulderInput.value = JSON.stringify(tempPoints);
            syncBoulderSummary();
            closeBoulderModal();
        });
    })();
</script>
@endsection
