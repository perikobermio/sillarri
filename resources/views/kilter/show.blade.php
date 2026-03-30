@extends('layouts.app', ['title' => 'Blokearen xehetasunak | KILTER'])

@section('content')
@php
    $boulderData = json_decode($block->boulder, true);
    $boulderData = is_array($boulderData) ? $boulderData : [];
    $viewerUser = auth()->user();
    $canDelete = $viewerUser && ((int) $viewerUser->id === (int) $block->user_id || (bool) $viewerUser->is_admin);
    $mapImageUrl = '';
    if ($block->map?->image) {
        $mapImageUrl = \Illuminate\Support\Str::startsWith($block->map->image, ['http://', 'https://', '/'])
            ? $block->map->image
            : '/storage/'.$block->map->image;
    }
@endphp

<section class="kilter-page">
    <div class="kilter-table-head">
        <div>
            <p class="eyebrow">Kilter Board Hub</p>
            <h1>{{ $block->name }}</h1>
        </div>
        <a class="btn btn-secondary" href="{{ route('kilter') }}">Itzuli zerrendara</a>
    </div>

    <div class="kilter-detail-grid {{ $mapImageUrl === '' ? 'is-single-panel' : '' }}">
        <article class="panel">
            <div class="detail-actions-row">
                @if($viewerUser)
                    <form method="POST" action="{{ route('kilter.toggleCompleted', $block) }}" class="detail-action-form">
                        @csrf
                        <button
                            type="submit"
                            class="btn {{ $isCompleted ? 'btn-success' : 'btn-secondary' }} detail-action-btn"
                            title="{{ $isCompleted ? 'Eginda markatua kendu' : 'Eginda markatu' }}"
                            aria-label="{{ $isCompleted ? 'Eginda markatua kendu' : 'Eginda markatu' }}"
                        >
                            <span class="action-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                                    <path d="M20 7L9 18l-5-5"></path>
                                </svg>
                            </span>
                        </button>
                    </form>
                @endif

                @if($canDelete)
                    <form method="POST" action="{{ route('kilter.destroy', $block) }}" class="detail-action-form" id="delete-block-form">
                        @csrf
                        @method('DELETE')
                        <button
                            type="button"
                            class="btn btn-danger detail-action-btn"
                            id="open-delete-confirm-modal"
                            title="Blokea ezabatu"
                            aria-label="Blokea ezabatu"
                        >
                            <span class="action-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                                    <path d="M5 7h14"></path>
                                    <path d="M10 11v6"></path>
                                    <path d="M14 11v6"></path>
                                    <path d="M8 7l1-2h6l1 2"></path>
                                    <path d="M7 7l1 12h8l1-12"></path>
                                </svg>
                            </span>
                        </button>
                    </form>
                @endif
            </div>

            @if(! $viewerUser)
                <p class="muted-note">Blokea eginda markatzeko saioa hasi behar duzu.</p>
            @endif
            @if(! $canDelete)
                <p class="muted-note">Ez daukazu bloke hau ezabatzeko baimenik.</p>
            @endif

            <hr class="detail-separator">
            <h3>Blokearen datuak</h3>
            <p><strong>ID:</strong> {{ $block->id }}</p>
            <p><strong>Deskribapena:</strong> {{ $block->description }}</p>
            <p><strong>Gradua:</strong> {{ $block->grade }}</p>
            <p><strong>Mapa:</strong> {{ $block->map?->name ?? '-' }}</p>
            <p><strong>Sortzailea:</strong> {{ $block->creator?->name ?? '-' }}</p>
            <p><strong>Sortua:</strong> {{ $block->created_at?->format('Y-m-d') ?? '-' }}</p>
        </article>

        @if($mapImageUrl !== '')
            <article class="panel">
                <h3>Mapa</h3>
                <div class="viewer-wrap detail-viewer-wrap">
                    <div class="viewer-stage">
                        <img id="detail-viewer-image" src="{{ $mapImageUrl }}" alt="Blokearen mapa">
                        <div id="detail-viewer-layer"></div>
                    </div>
                </div>
            </article>
        @endif
    </div>
</section>

@if($canDelete)
<div class="modal-shell hidden-modal" id="delete-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="delete-confirm-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="delete-confirm-title">Blokea ezabatu</h2>
            <button type="button" class="icon-btn" id="close-delete-confirm-modal" aria-label="Itxi leihoa">×</button>
        </div>
        <p class="confirm-delete-text">Ziur zaude <strong>{{ $block->name }}</strong> blokea betiko ezabatu nahi duzula?</p>
        <div class="kilter-form-actions">
            <button type="button" class="btn btn-danger" id="confirm-delete-block">Bai, ezabatu</button>
            <button type="button" class="btn btn-secondary" id="cancel-delete-block">Utzi</button>
        </div>
    </div>
</div>
@endif

@if($mapImageUrl !== '')
<script>
    (function () {
        const layer = document.getElementById('detail-viewer-layer');
        const image = document.getElementById('detail-viewer-image');
        if (!layer || !image) return;

        const validModes = ['points', 'line'];
        const validTypes = ['pie', 'mano_pie', 'comienzo', 'top'];
        const validSizes = ['pequeno', 'mediano', 'grande', 'gigante'];
        const raw = @json($boulderData);

        function normalize(payload) {
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

        function drawLine(from, to) {
            const wrap = layer.getBoundingClientRect();
            const x1 = (from.x / 100) * wrap.width;
            const y1 = (from.y / 100) * wrap.height;
            const x2 = (to.x / 100) * wrap.width;
            const y2 = (to.y / 100) * wrap.height;
            const dx = x2 - x1;
            const dy = y2 - y1;
            const lengthPx = Math.hypot(dx, dy);
            const angleDeg = Math.atan2(dy, dx) * (180 / Math.PI);
            const segment = document.createElement('span');
            segment.className = 'viewer-line';
            segment.style.left = `${x1}px`;
            segment.style.top = `${y1}px`;
            segment.style.width = `${lengthPx}px`;
            segment.style.transform = `rotate(${angleDeg}deg)`;
            return segment;
        }

        const state = normalize(raw);

        function renderOverlay() {
            if (!image.naturalWidth || !image.naturalHeight) return;
            const currentWidth = image.clientWidth || 0;
            const naturalWidth = image.naturalWidth || currentWidth || 1;
            const pointScale = currentWidth / naturalWidth;
            layer.style.setProperty('--point-scale', String(Math.max(0.5, Math.min(1.2, pointScale))));
            layer.innerHTML = '';

            if (state.mode === 'line') {
                for (let i = 0; i < state.points.length - 1; i += 1) {
                    layer.appendChild(drawLine(state.points[i], state.points[i + 1]));
                }
            }

            state.points.forEach((point) => {
                const marker = document.createElement('span');
                if (state.mode === 'line') {
                    marker.className = 'viewer-point line-node';
                } else {
                    marker.className = `viewer-point type-${point.type || 'mano_pie'} size-${point.size || 'mediano'}`;
                }
                marker.style.left = `${point.x}%`;
                marker.style.top = `${point.y}%`;
                layer.appendChild(marker);
            });
        }

        if (image.complete) {
            renderOverlay();
        } else {
            image.addEventListener('load', renderOverlay, { once: true });
        }
        window.addEventListener('resize', renderOverlay);
    })();
</script>
@endif

@if($canDelete)
<script>
    (function () {
        const modal = document.getElementById('delete-confirm-modal');
        const openBtn = document.getElementById('open-delete-confirm-modal');
        const closeBtn = document.getElementById('close-delete-confirm-modal');
        const cancelBtn = document.getElementById('cancel-delete-block');
        const confirmBtn = document.getElementById('confirm-delete-block');
        const form = document.getElementById('delete-block-form');

        if (!modal || !openBtn || !closeBtn || !cancelBtn || !confirmBtn || !form) return;

        function openModal() {
            modal.classList.remove('hidden-modal');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.add('hidden-modal');
            document.body.style.overflow = '';
        }

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        confirmBtn.addEventListener('click', () => form.submit());

        modal.addEventListener('click', (event) => {
            if (event.target === modal) closeModal();
        });
    })();
</script>
@endif
@endsection
