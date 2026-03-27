@extends('layouts.app', ['title' => 'Blokearen xehetasunak | KILTER'])

@section('content')
@php
    $boulderData = json_decode($block->boulder, true);
    $boulderData = is_array($boulderData) ? $boulderData : [];
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

    <div class="kilter-detail-grid">
        <article class="panel">
            <h3>Blokearen datuak</h3>
            <p><strong>ID:</strong> {{ $block->id }}</p>
            <p><strong>Deskribapena:</strong> {{ $block->description }}</p>
            <p><strong>Gradua:</strong> {{ $block->grade }}</p>
            <p><strong>Mapa:</strong> {{ $block->map?->name ?? '-' }}</p>
            <p><strong>Sortzailea:</strong> {{ $block->creator?->name ?? '-' }}</p>
            <p><strong>Sortua:</strong> {{ $block->created_at?->format('Y-m-d') ?? '-' }}</p>
        </article>

        <article class="panel">
            <h3>Mapa</h3>
            @if($mapImageUrl !== '')
                <div class="viewer-wrap detail-viewer-wrap">
                    <div class="viewer-stage">
                        <img id="detail-viewer-image" src="{{ $mapImageUrl }}" alt="Blokearen mapa">
                        <div id="detail-viewer-layer"></div>
                    </div>
                </div>
            @else
                <p>Bloke honek ez du maparik.</p>
            @endif
        </article>
    </div>
</section>

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
@endsection
