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
                        <option value="{{ $map->id }}" @selected((string) old('map_id') === (string) $map->id)>
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
            <input
                type="text"
                name="boulder"
                value="{{ old('boulder') }}"
                placeholder="[(120,82),(166,140),(244,195)]"
                required
            >
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

<script>
    (function () {
        const modal = document.getElementById('map-modal');
        const openBtn = document.getElementById('open-map-modal');
        const closeBtn = document.getElementById('close-map-modal');
        const cancelBtn = document.getElementById('cancel-map-modal');
        const form = document.getElementById('map-create-form');
        const errorBox = document.getElementById('map-modal-error');
        const select = document.getElementById('map-select');
        const saveBtn = document.getElementById('save-map-btn');
        const cameraInput = document.getElementById('map-image-camera');
        const fileInput = document.getElementById('map-image-file');

        if (!modal || !openBtn || !form || !select) return;

        const modalOpen = () => modal.classList.remove('hidden-modal');
        const modalClose = () => {
            modal.classList.add('hidden-modal');
            errorBox.classList.add('hidden-error');
            errorBox.textContent = '';
            form.reset();
        };

        openBtn.addEventListener('click', modalOpen);
        closeBtn?.addEventListener('click', modalClose);
        cancelBtn?.addEventListener('click', modalClose);

        modal.addEventListener('click', (event) => {
            if (event.target === modal) modalClose();
        });

        cameraInput?.addEventListener('change', () => {
            if (cameraInput.files && cameraInput.files.length > 0) {
                fileInput.value = '';
            }
        });

        fileInput?.addEventListener('change', () => {
            if (fileInput.files && fileInput.files.length > 0 && cameraInput) {
                cameraInput.value = '';
            }
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            errorBox.classList.add('hidden-error');
            errorBox.textContent = '';
            saveBtn.disabled = true;

            const payload = new FormData();
            payload.append('_token', form.querySelector('input[name="_token"]').value);
            payload.append('name', form.querySelector('#map-name').value);

            const cameraFile = cameraInput?.files?.[0];
            const file = fileInput?.files?.[0];
            const chosenFile = cameraFile || file;

            if (!chosenFile) {
                errorBox.textContent = 'Debes seleccionar una imagen o sacar una foto.';
                errorBox.classList.remove('hidden-error');
                saveBtn.disabled = false;
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
                    errorBox.textContent = msg;
                    errorBox.classList.remove('hidden-error');
                    saveBtn.disabled = false;
                    return;
                }

                const option = document.createElement('option');
                option.value = data.id;
                option.textContent = `${data.name} (ID ${data.id})`;
                option.selected = true;
                select.appendChild(option);
                select.value = String(data.id);

                modalClose();
            } catch (err) {
                errorBox.textContent = 'Error de red guardando el mapa.';
                errorBox.classList.remove('hidden-error');
            } finally {
                saveBtn.disabled = false;
            }
        });
    })();
</script>
@endsection
