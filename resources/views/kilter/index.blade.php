@extends('layouts.app', ['title' => 'KILTER | Sillarri Climb'])

@section('content')
<section class="kilter-page">
    <div class="kilter-table-wrap">
        <div class="kilter-table-head">
            <div>
                <p class="eyebrow">Kilter Board Hub</p>
                <h1>KILTER BLOKEAK</h1>
            </div>

            <div class="kilter-actions">
                @auth
                    <a class="btn btn-primary create-block-btn" href="{{ route('kilter.create') }}">
                        <span class="create-block-label">Blokea sortu</span>
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
                        <summary>Gradua</summary>
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
                            <label for="q-filter">Izena</label>
                            <input
                                id="q-filter"
                                type="text"
                                name="q"
                                value="{{ $search }}"
                                placeholder="Bilatu blokea izenaren arabera..."
                            >

                            <label for="creator-filter">Erabiltzailea</label>
                            <select id="creator-filter" name="creator">
                                <option value="">Denak</option>
                                @foreach($creators as $creator)
                                    <option
                                        value="{{ $creator->id }}"
                                        @selected($selectedCreator !== null && (int) $creator->id === (int) $selectedCreator)
                                    >
                                        {{ $creator->name }}
                                    </option>
                                @endforeach
                            </select>

                            @auth
                                <label for="completed-filter">Egoera</label>
                                <select id="completed-filter" name="completed">
                                    <option value="all" @selected(($selectedCompletedFilter ?? 'all') === 'all')>Denak</option>
                                    <option value="done" @selected(($selectedCompletedFilter ?? 'all') === 'done')>Eginda</option>
                                    <option value="pending" @selected(($selectedCompletedFilter ?? 'all') === 'pending')>Egin gabe</option>
                                </select>
                            @endauth
                        </div>
                    </details>

                    <button type="submit" class="btn btn-primary search-submit-btn">
                        <span class="search-submit-label">Bilatu</span>
                    </button>
                    @if($search !== '' || count($selectedGrades) > 0 || $selectedCreator !== null || (($selectedCompletedFilter ?? 'all') !== 'all'))
                        <a class="btn btn-secondary" href="{{ route('kilter') }}">Garbitu</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-scroll">
            <table class="kilter-table">
                <thead>
                    <tr>
                        <th class="col-detail">Xehet.</th>
                        <th class="col-id">ID</th>
                        <th>Izena</th>
                        <th class="col-description">Deskribapena</th>
                        <th>Gradua</th>
                        <th>Mapa</th>
                        <th>Sortzailea</th>
                        <th class="col-created">Sortua</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blocks as $block)
                        @php
                            $boulderData = json_decode($block->boulder, true);
                            $boulderData = is_array($boulderData) ? $boulderData : [];
                            $isCompleted = in_array((int) $block->id, $completedBlockIds ?? [], true);
                            $mapImageUrl = '';
                            if ($block->map?->image) {
                                $mapImageUrl = \Illuminate\Support\Str::startsWith($block->map->image, ['http://', 'https://', '/'])
                                    ? $block->map->image
                                    : '/storage/'.$block->map->image;
                            }
                        @endphp
                        <tr>
                            <td colspan="8" class="row-padding-none">
                                <div class="block-row-wrap">
                                    <a
                                        class="block-detail-link"
                                        href="{{ route('kilter.show', $block) }}"
                                        aria-label="Blokearen xehetasunak ikusi"
                                        title="Xehetasunak"
                                    >⋮</a>
                                    <button
                                        type="button"
                                        class="block-row-btn {{ $mapImageUrl !== '' ? 'is-clickable' : '' }} {{ $isCompleted ? 'is-completed' : '' }}"
                                        @if($mapImageUrl !== '')
                                            data-image-url="{{ $mapImageUrl }}"
                                            data-points='@json($boulderData)'
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">Ez dago blokerik uneko iragazkiarekin.</td>
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
            <button type="button" class="btn btn-secondary" id="close-boulder-viewer">Itzuli</button>
        </div>
        <div class="viewer-wrap" id="viewer-wrap">
            <div class="viewer-stage" id="viewer-stage">
                <img id="viewer-image" alt="Boulder mapa">
                <div id="viewer-layer"></div>
            </div>
        </div>
        <p id="viewer-map-error" class="error hidden-error viewer-map-error">Errorea maparen irudia kargatzean.</p>
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
        const viewerWrap = document.getElementById('viewer-wrap');
        const viewerMapError = document.getElementById('viewer-map-error');
        const buttons = document.querySelectorAll('.block-row-btn.is-clickable');

        if (!viewer || !viewerImage || !viewerLayer || !viewerStage || !viewerWrap) return;

        let zoom = 1;
        let isPanning = false;
        let panStartX = 0;
        let panStartY = 0;
        let panStartScrollLeft = 0;
        let panStartScrollTop = 0;
        let panDistance = 0;
        const activeTouchPoints = new Map();
        let pinchStartDistance = 0;
        let pinchStartZoom = 1;
        let baseImageWidth = 0;
        let applyWidthFitOnLoad = false;
        let currentState = { mode: 'points', points: [] };
        const validModes = ['points', 'line'];
        const validTypes = ['pie', 'mano_pie', 'comienzo', 'top'];
        const validSizes = ['pequeno', 'mediano', 'grande', 'gigante'];
        const minZoom = 0.01;
        const maxZoom = 3;

        function hasFinePointer() {
            return window.matchMedia('(pointer: fine)').matches;
        }

        function syncBodyScrollLock() {
            const hasOpenModal = document.querySelector('.modal-shell:not(.hidden-modal)') !== null;
            document.body.style.overflow = hasOpenModal ? 'hidden' : '';
        }

        function distanceBetweenTouches() {
            const points = Array.from(activeTouchPoints.values());
            if (points.length < 2) return 0;
            const dx = points[0].x - points[1].x;
            const dy = points[0].y - points[1].y;
            return Math.hypot(dx, dy);
        }

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

        function drawLine(from, to, className) {
            const layerWidth = viewerLayer.clientWidth || viewerImage.clientWidth || 0;
            const layerHeight = viewerLayer.clientHeight || viewerImage.clientHeight || 0;
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

        function setZoom(value) {
            zoom = Math.min(maxZoom, Math.max(minZoom, value));
            if (!baseImageWidth) {
                baseImageWidth = Math.max(1, viewerImage.naturalWidth || viewerWrap.clientWidth || 680);
            }
            const renderedWidth = Math.max(1, Math.round(baseImageWidth * zoom));
            const pointScale = Math.max(0.7, renderedWidth / 680);
            viewerImage.style.width = `${renderedWidth}px`;
            viewerLayer.style.setProperty('--point-scale', String(pointScale));
            requestAnimationFrame(renderCurrentState);
        }

        function getWidthFitZoom() {
            const wrapWidth = viewerWrap.clientWidth || 1;
            const naturalWidth = viewerImage.naturalWidth || baseImageWidth || wrapWidth;
            return wrapWidth / naturalWidth;
        }

        function closeViewer() {
            viewer.classList.add('hidden-modal');
            viewerImage.src = '';
            viewerLayer.innerHTML = '';
            if (viewerMapError) {
                viewerMapError.classList.add('hidden-error');
                viewerMapError.textContent = 'Errorea maparen irudia kargatzean.';
            }
            currentState = { mode: 'points', points: [] };
            applyWidthFitOnLoad = false;
            activeTouchPoints.clear();
            isPanning = false;
            setZoom(1);
            syncBodyScrollLock();
        }

        function renderCurrentState() {
            viewerLayer.innerHTML = '';

            if (currentState.mode === 'line') {
                for (let i = 0; i < currentState.points.length - 1; i += 1) {
                    const segment = drawLine(currentState.points[i], currentState.points[i + 1], 'viewer-line');
                    if (segment) viewerLayer.appendChild(segment);
                }
            }

            currentState.points.forEach((point) => {
                if (typeof point?.x !== 'number' || typeof point?.y !== 'number') return;
                const marker = document.createElement('span');
                if (currentState.mode === 'line') {
                    marker.className = 'viewer-point line-node';
                } else {
                    const type = point?.type || 'mano_pie';
                    const size = point?.size || 'mediano';
                    marker.className = `viewer-point type-${type} size-${size}`;
                }
                marker.style.left = `${point.x}%`;
                marker.style.top = `${point.y}%`;
                viewerLayer.appendChild(marker);
            });
        }

        buttons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const imageUrl = btn.dataset.imageUrl || '';
                const title = btn.dataset.title || 'Blokea';
                let state = { mode: 'points', points: [] };

                try {
                    const parsed = JSON.parse(btn.dataset.points || '[]');
                    state = normalizeBoulderState(parsed);
                } catch {
                    state = { mode: 'points', points: [] };
                }
                currentState = state;

                viewerTitle.textContent = title;
                viewerImage.src = imageUrl;
                viewerLayer.innerHTML = '';
                if (viewerMapError) {
                    viewerMapError.classList.add('hidden-error');
                    viewerMapError.textContent = 'Errorea maparen irudia kargatzean.';
                }

                activeTouchPoints.clear();
                isPanning = false;
                pinchStartDistance = 0;
                pinchStartZoom = zoom;
                baseImageWidth = 0;
                applyWidthFitOnLoad = true;
                viewerWrap.scrollLeft = 0;
                viewerWrap.scrollTop = 0;
                viewerWrap.style.cursor = hasFinePointer() ? 'grab' : 'default';
                viewer.classList.remove('hidden-modal');
                syncBodyScrollLock();
            });
        });

        viewerImage.addEventListener('load', () => {
            if (viewerMapError) {
                viewerMapError.classList.add('hidden-error');
            }
            baseImageWidth = Math.max(1, viewerImage.naturalWidth || viewerWrap.clientWidth || 680);
            if (applyWidthFitOnLoad) {
                if ((viewerWrap.clientWidth || 0) < 10) {
                    requestAnimationFrame(() => {
                        if (!applyWidthFitOnLoad) return;
                        setZoom(getWidthFitZoom());
                        applyWidthFitOnLoad = false;
                    });
                    return;
                }
                setZoom(getWidthFitZoom());
                applyWidthFitOnLoad = false;
            } else {
                setZoom(zoom);
            }
            renderCurrentState();
        });

        viewerImage.addEventListener('error', () => {
            viewerLayer.innerHTML = '';
            currentState = { mode: 'points', points: [] };
            if (viewerMapError) {
                viewerMapError.textContent = 'Errorea maparen irudia kargatzean. Egiaztatu maparen fitxategia.';
                viewerMapError.classList.remove('hidden-error');
            }
        });

        viewerWrap.addEventListener('mousedown', (event) => {
            if (!hasFinePointer()) return;
            if (event.button !== 0) return;

            isPanning = true;
            panDistance = 0;
            panStartX = event.clientX;
            panStartY = event.clientY;
            panStartScrollLeft = viewerWrap.scrollLeft;
            panStartScrollTop = viewerWrap.scrollTop;
            viewerWrap.style.cursor = 'grabbing';
            event.preventDefault();
        });

        window.addEventListener('mousemove', (event) => {
            if (!isPanning) return;

            const deltaX = event.clientX - panStartX;
            const deltaY = event.clientY - panStartY;
            panDistance = Math.max(panDistance, Math.abs(deltaX) + Math.abs(deltaY));

            viewerWrap.scrollLeft = panStartScrollLeft - deltaX;
            viewerWrap.scrollTop = panStartScrollTop - deltaY;
        });

        window.addEventListener('mouseup', () => {
            if (!isPanning) return;
            isPanning = false;
            viewerWrap.style.cursor = hasFinePointer() ? 'grab' : 'default';
        });

        viewerWrap.addEventListener('wheel', (event) => {
            if (!hasFinePointer()) return;
            event.preventDefault();
            const step = event.deltaY < 0 ? 0.12 : -0.12;
            setZoom(zoom + step);
        }, { passive: false });

        viewerWrap.addEventListener('touchstart', (event) => {
            for (const touch of event.changedTouches) {
                activeTouchPoints.set(touch.identifier, { x: touch.clientX, y: touch.clientY });
            }

            if (activeTouchPoints.size === 1) {
                const point = Array.from(activeTouchPoints.values())[0];
                isPanning = true;
                panDistance = 0;
                panStartX = point.x;
                panStartY = point.y;
                panStartScrollLeft = viewerWrap.scrollLeft;
                panStartScrollTop = viewerWrap.scrollTop;
            } else if (activeTouchPoints.size >= 2) {
                isPanning = false;
                pinchStartDistance = distanceBetweenTouches();
                pinchStartZoom = zoom;
            }
        }, { passive: true });

        viewerWrap.addEventListener('touchmove', (event) => {
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

            viewerWrap.scrollLeft = panStartScrollLeft - deltaX;
            viewerWrap.scrollTop = panStartScrollTop - deltaY;
            event.preventDefault();
        }, { passive: false });

        viewerWrap.addEventListener('touchend', (event) => {
            for (const touch of event.changedTouches) {
                activeTouchPoints.delete(touch.identifier);
            }

            if (activeTouchPoints.size === 1) {
                const point = Array.from(activeTouchPoints.values())[0];
                isPanning = true;
                panDistance = 0;
                panStartX = point.x;
                panStartY = point.y;
                panStartScrollLeft = viewerWrap.scrollLeft;
                panStartScrollTop = viewerWrap.scrollTop;
                pinchStartDistance = 0;
                pinchStartZoom = zoom;
                return;
            }

            isPanning = false;
            pinchStartDistance = 0;
            pinchStartZoom = zoom;
        }, { passive: true });

        viewerWrap.addEventListener('touchcancel', (event) => {
            for (const touch of event.changedTouches) {
                activeTouchPoints.delete(touch.identifier);
            }
            isPanning = false;
            pinchStartDistance = 0;
            pinchStartZoom = zoom;
        }, { passive: true });

        closeBtn?.addEventListener('click', closeViewer);
        viewer.addEventListener('click', (event) => {
            if (event.target === viewer) closeViewer();
        });
    })();
</script>
@endsection
