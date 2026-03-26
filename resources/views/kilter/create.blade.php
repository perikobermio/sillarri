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
                                    : asset('storage/'.$map->image);
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
                    Añadir mapa
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

        const openMapModal = () => mapModal.classList.remove('hidden-modal');
        const closeMapModal = () => {
            mapModal.classList.add('hidden-modal');
            mapError.classList.add('hidden-error');
            mapError.textContent = '';
            mapForm.reset();
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
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    let msg = data?.message || 'No se ha podido guardar el mapa.';
                    if (data?.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        const firstError = firstKey ? data.errors[firstKey]?.[0] : null;
                        if (firstError) msg = firstError;
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
            } catch {
                mapError.textContent = 'Error de red guardando el mapa.';
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
        const coordCount = document.getElementById('coord-count');
        const pointTypeSelect = document.getElementById('point-type');
        const pointSizeSelect = document.getElementById('point-size');

        const zoomInBtn = document.getElementById('zoom-in');
        const zoomOutBtn = document.getElementById('zoom-out');
        const zoomResetBtn = document.getElementById('zoom-reset');
        const clearPointsBtn = document.getElementById('clear-points');

        let tempPoints = [];
        let zoom = 1;

        function setZoom(value) {
            zoom = Math.min(3, Math.max(0.5, value));
            coordStage.style.transform = `scale(${zoom})`;
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
            renderPoints();
            boulderModal.classList.remove('hidden-modal');
        }

        function closeBoulderModal() {
            boulderModal.classList.add('hidden-modal');
            boulderError.classList.add('hidden-error');
            boulderError.textContent = '';
        }

        openBoulderBtn?.addEventListener('click', openBoulderModal);
        closeBoulderBtn?.addEventListener('click', closeBoulderModal);
        cancelBoulderBtn?.addEventListener('click', closeBoulderModal);
        boulderModal?.addEventListener('click', (event) => {
            if (event.target === boulderModal) closeBoulderModal();
        });

        coordImage?.addEventListener('click', (event) => {
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

        zoomInBtn?.addEventListener('click', () => setZoom(zoom + 0.2));
        zoomOutBtn?.addEventListener('click', () => setZoom(zoom - 0.2));
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
