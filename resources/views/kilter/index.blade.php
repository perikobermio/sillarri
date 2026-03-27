@extends('layouts.app', ['title' => 'KILTER | Sillarri Climb'])

@section('content')
<section class="kilter-page">
    <div class="kilter-table-wrap">
        <div class="kilter-table-head">
            <div>
                <p class="eyebrow">Kilter Board Hub</p>
                <h1>KILTER BLOQUES</h1>
            </div>

            <div class="kilter-actions">
                @auth
                    <a class="btn btn-primary create-block-btn" href="{{ route('kilter.create') }}">
                        <span class="create-block-label">Crear bloque</span>
                    </a>
                @endauth

                <form method="GET" action="{{ route('kilter') }}" class="kilter-search">
                    @php
                        $romanMap = [
                            '5' => 'V',
                            '6' => 'VI',
                            '7' => 'VII',
                            '8' => 'VIII',
                            '9' => 'IX',
                        ];
                        $groupedGrades = [];
                        foreach ($grades as $g) {
                            $groupKey = preg_replace('/[^0-9]/', '', $g);
                            $groupedGrades[$groupKey][] = $g;
                        }
                    @endphp
                    <details class="grade-filter-box">
                        <summary>Grade</summary>
                        <div class="grade-check-list">
                            @foreach($groupedGrades as $group => $list)
                                <div class="grade-group-title">{{ $romanMap[$group] ?? $group }}</div>
                                @foreach($list as $g)
                                    @php $gradeId = 'grade_'.strtolower(str_replace('+', '_plus', $g)); @endphp
                                    <div class="grade-check-item">
                                        <input
                                            id="{{ $gradeId }}"
                                            type="checkbox"
                                            name="grade[]"
                                            value="{{ $g }}"
                                            @checked(in_array(strtolower($g), $selectedGrades, true))
                                        >
                                        <label for="{{ $gradeId }}">{{ strtoupper($g) }}</label>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </details>

                    <details class="extra-filter-box">
                        <summary>+</summary>
                        <div class="extra-filter-panel">
                            <label for="q-filter">Nombre</label>
                            <input
                                id="q-filter"
                                type="text"
                                name="q"
                                value="{{ $search }}"
                                placeholder="Buscar bloque por nombre..."
                            >

                            <label for="creator-filter">Usuario</label>
                            <select id="creator-filter" name="creator">
                                <option value="">Todos</option>
                                @foreach($creators as $creator)
                                    <option
                                        value="{{ $creator->id }}"
                                        @selected($selectedCreator !== null && (int) $creator->id === (int) $selectedCreator)
                                    >
                                        {{ $creator->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </details>

                    <button type="submit" class="btn btn-primary search-submit-btn">
                        <span class="search-submit-label">Buscar</span>
                    </button>
                    @if($search !== '' || count($selectedGrades) > 0 || $selectedCreator !== null)
                        <a class="btn btn-secondary" href="{{ route('kilter') }}">Limpiar</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-scroll">
            <table class="kilter-table">
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th>Nombre</th>
                        <th class="col-description">Descripcion</th>
                        <th>Grado</th>
                        <th>Mapa</th>
                        <th>Creador</th>
                        <th class="col-created">Creado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blocks as $block)
                        @php
                            $points = json_decode($block->boulder, true);
                            $points = is_array($points) ? $points : [];
                            $mapImageUrl = '';
                            if ($block->map?->image) {
                                $mapImageUrl = \Illuminate\Support\Str::startsWith($block->map->image, ['http://', 'https://', '/'])
                                    ? $block->map->image
                                    : '/storage/'.$block->map->image;
                            }
                        @endphp
                        <tr>
                            <td colspan="7" class="row-padding-none">
                                <button
                                    type="button"
                                    class="block-row-btn {{ $mapImageUrl !== '' ? 'is-clickable' : '' }}"
                                    @if($mapImageUrl !== '')
                                        data-image-url="{{ $mapImageUrl }}"
                                        data-points='@json($points)'
                                        data-title="{{ $block->name }}"
                                    @else
                                        disabled
                                    @endif
                                >
                                    <span class="col-id">{{ $block->id }}</span>
                                    <span>{{ $block->name }}</span>
                                    <span class="col-description">{{ $block->description }}</span>
                                    <span>{{ $block->grade }}</span>
                                    <span>{{ $block->map?->name ?? '-' }}</span>
                                    <span>{{ $block->creator?->name ?? '-' }}</span>
                                    <span class="col-created">{{ $block->created_at?->format('Y-m-d') }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No hay bloques para el filtro actual.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="modal-shell hidden-modal" id="boulder-viewer" role="dialog" aria-modal="true" aria-labelledby="boulder-viewer-title">
    <div class="modal-card modal-card-xl">
        <div class="modal-head">
            <h2 id="boulder-viewer-title">Boulder</h2>
            <button type="button" class="btn btn-secondary" id="close-boulder-viewer">Volver</button>
        </div>
        <div class="viewer-toolbar">
            <button type="button" class="btn btn-secondary" id="viewer-zoom-out">- Zoom</button>
            <button type="button" class="btn btn-secondary" id="viewer-zoom-in">+ Zoom</button>
            <button type="button" class="btn btn-secondary" id="viewer-zoom-reset">Reset</button>
            <span id="viewer-zoom-label">100%</span>
        </div>
        <div class="viewer-wrap">
            <div class="viewer-stage" id="viewer-stage">
                <img id="viewer-image" alt="Mapa del boulder">
                <div id="viewer-layer"></div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const filterDetails = document.querySelectorAll('.grade-filter-box, .extra-filter-box');

        document.addEventListener('click', (event) => {
            filterDetails.forEach((detailsEl) => {
                if (!detailsEl?.open) return;
                if (detailsEl.contains(event.target)) return;
                detailsEl.open = false;
            });
        });

        const viewer = document.getElementById('boulder-viewer');
        const closeBtn = document.getElementById('close-boulder-viewer');
        const viewerTitle = document.getElementById('boulder-viewer-title');
        const viewerImage = document.getElementById('viewer-image');
        const viewerLayer = document.getElementById('viewer-layer');
        const viewerStage = document.getElementById('viewer-stage');
        const buttons = document.querySelectorAll('.block-row-btn.is-clickable');
        const zoomInBtn = document.getElementById('viewer-zoom-in');
        const zoomOutBtn = document.getElementById('viewer-zoom-out');
        const zoomResetBtn = document.getElementById('viewer-zoom-reset');
        const zoomLabel = document.getElementById('viewer-zoom-label');

        if (!viewer || !viewerImage || !viewerLayer || !viewerStage) return;

        let zoom = 1;

        function setZoom(value) {
            zoom = Math.min(4, Math.max(0.5, value));
            viewerStage.style.transform = `scale(${zoom})`;
            zoomLabel.textContent = `${Math.round(zoom * 100)}%`;
        }

        function closeViewer() {
            viewer.classList.add('hidden-modal');
            viewerImage.src = '';
            viewerLayer.innerHTML = '';
            setZoom(1);
        }

        buttons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const imageUrl = btn.dataset.imageUrl || '';
                const title = btn.dataset.title || 'Boulder';
                let points = [];

                try {
                    const parsed = JSON.parse(btn.dataset.points || '[]');
                    points = Array.isArray(parsed) ? parsed : [];
                } catch {
                    points = [];
                }

                viewerTitle.textContent = title;
                viewerImage.src = imageUrl;
                viewerLayer.innerHTML = '';

                points.forEach((point) => {
                    if (typeof point?.x !== 'number' || typeof point?.y !== 'number') return;
                    const marker = document.createElement('span');
                    const type = point?.type || 'mano_pie';
                    const size = point?.size || 'mediano';
                    marker.className = `viewer-point type-${type} size-${size}`;
                    marker.style.left = `${point.x}%`;
                    marker.style.top = `${point.y}%`;
                    viewerLayer.appendChild(marker);
                });

                setZoom(1);
                viewer.classList.remove('hidden-modal');
            });
        });

        zoomInBtn?.addEventListener('click', () => setZoom(zoom + 0.2));
        zoomOutBtn?.addEventListener('click', () => setZoom(zoom - 0.2));
        zoomResetBtn?.addEventListener('click', () => setZoom(1));
        closeBtn?.addEventListener('click', closeViewer);
        viewer.addEventListener('click', (event) => {
            if (event.target === viewer) closeViewer();
        });
    })();
</script>
@endsection
