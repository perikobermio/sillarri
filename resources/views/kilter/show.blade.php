@extends('layouts.app', ['title' => 'Blokearen xehetasunak | KILTER'])

@section('content')
@php
    $boulderData = json_decode($block->boulder, true);
    $boulderData = is_array($boulderData) ? $boulderData : [];
    $viewerUser = auth()->user();
    $canEdit = $viewerUser && ((int) $viewerUser->id === (int) $block->user_id || (bool) $viewerUser->is_admin);
    $canDelete = $viewerUser && ((int) $viewerUser->id === (int) $block->user_id || (bool) $viewerUser->is_admin);
    $ratingToColor = static function (float $value): string {
        $clamped = max(1.0, min(10.0, $value));
        $ratio = ($clamped - 1.0) / 9.0;
        $hue = 220.0 * (1.0 - $ratio);
        return 'hsl('.round($hue, 1).' 78% 45%)';
    };
    $userVoteColor = $userVote !== null ? $ratingToColor((float) $userVote) : null;
    $mapImageUrl = '';
    if ($block->map?->image) {
        $mapImageUrl = \Illuminate\Support\Str::startsWith($block->map->image, ['http://', 'https://', '/'])
            ? $block->map->image
            : '/storage/'.$block->map->image;
    }
    $recotationCounts = $recotationSummary['counts'] ?? [];
    $recotationTop = $recotationSummary['top'] ?? null;
    $recotationTotal = (int) ($recotationSummary['total'] ?? 0);
    $recotationEntries = $recotationEntries ?? [];
    $canSuggestRecote = $viewerUser && ((int) $viewerUser->id !== (int) $block->user_id);
    $canResolveRecote = $viewerUser && (((int) $viewerUser->id === (int) $block->user_id) || (bool) $viewerUser->is_admin);
@endphp

<section class="kilter-page">
    <div class="kilter-table-head">
        <div>
            <p class="eyebrow">Kilter Board Hub</p>
            <h1>
                {{ $block->name }}
                <span class="recote-top-grade">{{ strtoupper($block->grade) }}</span>
            </h1>
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

                    <button
                        type="button"
                        class="btn {{ $userVoteColor ? 'btn-vote-colored' : 'btn-secondary' }} detail-action-btn"
                        id="open-vote-modal"
                        title="Balorazioa eman"
                        aria-label="Balorazioa eman"
                        @if($userVoteColor)
                            style="--vote-color: {{ $userVoteColor }}"
                        @endif
                    >
                        <span class="action-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                                <path d="M12 3l2.8 5.7 6.2.9-4.5 4.4 1 6.2-5.5-2.9-5.5 2.9 1-6.2-4.5-4.4 6.2-.9z"></path>
                            </svg>
                        </span>
                    </button>
                @endif

                @if($canSuggestRecote)
                    <button
                        type="button"
                        class="btn btn-secondary detail-action-btn {{ $userRecote ? 'is-active' : '' }}"
                        id="open-recote-modal"
                        title="Rekotazioa {{ $userRecote ? strtoupper($userRecote) : '' }}"
                        aria-label="Rekotazioa {{ $userRecote ? strtoupper($userRecote) : '' }}"
                    >
                        <span class="action-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                                <path d="M12 3v3"></path>
                                <path d="M12 18v3"></path>
                                <path d="M4.2 7l2.1 2.1"></path>
                                <path d="M17.7 17.7l2.1 2.1"></path>
                                <path d="M3 12h3"></path>
                                <path d="M18 12h3"></path>
                                <path d="M4.2 17l2.1-2.1"></path>
                                <path d="M17.7 6.3l2.1-2.1"></path>
                            </svg>
                        </span>
                    </button>
                @endif

                @if($canEdit)
                    <a
                        href="{{ route('kilter.edit', $block) }}"
                        class="btn btn-secondary detail-action-btn"
                        title="Blokea editatu"
                        aria-label="Blokea editatu"
                    >
                        <span class="action-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                                <path d="M4 20h4l10-10-4-4L4 16z"></path>
                                <path d="M13 7l4 4"></path>
                            </svg>
                        </span>
                    </a>
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

            <hr class="detail-separator">
            <h3>Blokearen datuak</h3>
            <p><strong>ID:</strong> {{ $block->id }}</p>
            <p><strong>Deskribapena:</strong> {{ $block->description }}</p>
            <p><strong>Gradua:</strong> {{ $block->grade }}</p>
            <p><strong>Kokapena:</strong> {{ $block->kokapena ?? '-' }}</p>
            <p><strong>Balorazioa:</strong> {{ number_format($ratingAverage, 1) }}/10 ({{ $ratingCount }} bozka)</p>
            <p><strong>Mapa:</strong> {{ $block->map?->name ?? '-' }}</p>
            <p><strong>Sortzailea:</strong> {{ $block->creator?->name ?? '-' }}</p>
            <p><strong>Sortua:</strong> {{ $block->created_at?->format('Y-m-d') ?? '-' }}</p>
        </article>

        @if($recotationTotal > 0)
        <article class="panel">
            <h3>
                Rekotazioa
                @if($recotationTop)
                    <span class="recote-top-grade">{{ strtoupper((string) $recotationTop) }}</span>
                @endif
            </h3>
            <div class="recote-section-head">
                <p><strong>Proposamenak:</strong></p>
                @if($canResolveRecote && $recotationTop)
                    <div class="recote-actions">
                        <form method="POST" action="{{ route('kilter.recote.resolve', $block) }}" class="detail-action-form">
                            @csrf
                            <input type="hidden" name="decision" value="accept">
                            <button type="submit" class="btn btn-primary detail-action-btn" title="Onartu gradua" aria-label="Onartu gradua">
                                <span class="action-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                                        <path d="M5 12l4 4 10-10"></path>
                                    </svg>
                                </span>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('kilter.recote.resolve', $block) }}" class="detail-action-form">
                            @csrf
                            <input type="hidden" name="decision" value="reject">
                            <button type="submit" class="btn btn-secondary detail-action-btn" title="Baztertu rekotazioak" aria-label="Baztertu rekotazioak">
                                <span class="action-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                                        <path d="M6 6l12 12"></path>
                                        <path d="M18 6l-12 12"></path>
                                    </svg>
                                </span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
            <ul class="recote-list">
                @foreach($recotationEntries as $entry)
                    <li>
                        <span class="recote-user">{{ $entry['username'] ?? '-' }}</span>
                        <span class="recote-grade">{{ strtoupper((string) ($entry['grade'] ?? '')) }}</span>
                    </li>
                @endforeach
            </ul>
        </article>
        @endif

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

@if($viewerUser)
<div class="modal-shell hidden-modal" id="vote-modal" role="dialog" aria-modal="true" aria-labelledby="vote-modal-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="vote-modal-title">Balorazioa</h2>
            <button type="button" class="icon-btn" id="close-vote-modal" aria-label="Itxi leihoa">×</button>
        </div>
        <form method="POST" action="{{ route('kilter.vote', $block) }}" id="vote-form" class="kilter-form">
            @csrf
            <input type="hidden" name="value" id="vote-value-input" value="{{ number_format((float) ($userVote ?? 5), 1, '.', '') }}">
            <div class="vote-stars" id="vote-stars" role="button" tabindex="0" aria-label="Balorazioa hautatu">
                <span class="vote-stars-base" aria-hidden="true">☆☆☆☆☆☆☆☆☆☆</span>
                <span class="vote-stars-fill" id="vote-stars-fill" aria-hidden="true">★★★★★★★★★★</span>
            </div>
            <p class="vote-value-text"><strong id="vote-value-label">{{ number_format((float) ($userVote ?? 5), 1) }}</strong> / 10</p>
            <div class="kilter-form-actions">
                <button type="submit" class="btn btn-primary">Gorde bozka</button>
                <button type="button" class="btn btn-secondary" id="cancel-vote-modal">Utzi</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($canSuggestRecote)
<div class="modal-shell hidden-modal" id="recote-modal" role="dialog" aria-modal="true" aria-labelledby="recote-modal-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="recote-modal-title">Recotatu blokea</h2>
            <button type="button" class="icon-btn" id="close-recote-modal" aria-label="Itxi leihoa">×</button>
        </div>
        <form method="POST" action="{{ route('kilter.recote', $block) }}" class="kilter-form">
            @csrf
            <label for="recote-grade">Gradu berria</label>
            <select name="grade" id="recote-grade" required>
                @foreach($grades as $grade)
                    <option value="{{ $grade }}" @selected($userRecote === $grade)>{{ $grade }}</option>
                @endforeach
            </select>
            <div class="kilter-form-actions">
                <button type="submit" class="btn btn-primary">Rekotatu</button>
                <button type="button" class="btn btn-secondary" id="cancel-recote-modal">Utzi</button>
            </div>
        </form>
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
            const width = image.clientWidth || layer.clientWidth || 0;
            const height = image.clientHeight || layer.clientHeight || 0;
            if (!width || !height) return null;

            const x1 = (from.x / 100) * width;
            const y1 = (from.y / 100) * height;
            const x2 = (to.x / 100) * width;
            const y2 = (to.y / 100) * height;
            const dx = x2 - x1;
            const dy = y2 - y1;
            const lengthPx = Math.hypot(dx, dy);
            if (!lengthPx) return null;
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
            const currentHeight = image.clientHeight || 0;
            if (!currentWidth || !currentHeight) {
                requestAnimationFrame(renderOverlay);
                return;
            }
            const pointScale = currentWidth / naturalWidth;
            layer.style.setProperty('--point-scale', String(Math.max(0.5, Math.min(1.2, pointScale))));
            layer.innerHTML = '';

            if (state.mode === 'line') {
                for (let i = 0; i < state.points.length - 1; i += 1) {
                    const segment = drawLine(state.points[i], state.points[i + 1]);
                    if (segment) layer.appendChild(segment);
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

@if($viewerUser)
<script>
    (function () {
        const modal = document.getElementById('vote-modal');
        const openBtn = document.getElementById('open-vote-modal');
        const closeBtn = document.getElementById('close-vote-modal');
        const cancelBtn = document.getElementById('cancel-vote-modal');
        const stars = document.getElementById('vote-stars');
        const starsFill = document.getElementById('vote-stars-fill');
        const valueInput = document.getElementById('vote-value-input');
        const valueLabel = document.getElementById('vote-value-label');

        if (!modal || !openBtn || !closeBtn || !cancelBtn || !stars || !starsFill || !valueInput || !valueLabel) return;

        function setModalOpen(isOpen) {
            modal.classList.toggle('hidden-modal', !isOpen);
            document.body.style.overflow = isOpen ? 'hidden' : '';
        }

        function clampVote(value) {
            const rounded = Math.round(value * 2) / 2;
            return Math.max(1, Math.min(10, rounded));
        }

        function applyVote(value) {
            const vote = clampVote(value);
            valueInput.value = vote.toFixed(1);
            valueLabel.textContent = vote.toFixed(1);
            starsFill.style.width = `${(vote / 10) * 100}%`;
        }

        function voteFromPointer(clientX) {
            const rect = stars.getBoundingClientRect();
            const x = Math.max(0, Math.min(rect.width, clientX - rect.left));
            if (rect.width <= 0) return 5;
            if (x >= rect.width - 1) return 10;
            const step = Math.max(1, Math.min(20, Math.ceil((x / rect.width) * 20)));
            return clampVote(step / 2);
        }

        openBtn.addEventListener('click', () => {
            applyVote(parseFloat(valueInput.value) || 5);
            setModalOpen(true);
        });
        closeBtn.addEventListener('click', () => setModalOpen(false));
        cancelBtn.addEventListener('click', () => setModalOpen(false));
        modal.addEventListener('click', (event) => {
            if (event.target === modal) setModalOpen(false);
        });

        function handlePointer(clientX, commit) {
            const value = voteFromPointer(clientX);
            if (commit) {
                applyVote(value);
            } else {
                starsFill.style.width = `${(value / 10) * 100}%`;
            }
        }

        stars.addEventListener('click', (event) => {
            handlePointer(event.clientX, true);
        });
        stars.addEventListener('mousemove', (event) => {
            handlePointer(event.clientX, false);
        });
        stars.addEventListener('mouseleave', () => {
            applyVote(parseFloat(valueInput.value) || 5);
        });
        stars.addEventListener('touchstart', (event) => {
            const touch = event.touches[0];
            if (!touch) return;
            handlePointer(touch.clientX, true);
        }, { passive: true });
        stars.addEventListener('touchmove', (event) => {
            const touch = event.touches[0];
            if (!touch) return;
            handlePointer(touch.clientX, false);
        }, { passive: true });
        stars.addEventListener('keydown', (event) => {
            const current = parseFloat(valueInput.value) || 5;
            if (event.key === 'ArrowRight') {
                event.preventDefault();
                applyVote(current + 0.5);
            }
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                applyVote(current - 0.5);
            }
        });
    })();
</script>
@endif

@if($canSuggestRecote)
<script>
    (function () {
        const modal = document.getElementById('recote-modal');
        const openBtn = document.getElementById('open-recote-modal');
        const closeBtn = document.getElementById('close-recote-modal');
        const cancelBtn = document.getElementById('cancel-recote-modal');

        if (!modal || !openBtn || !closeBtn || !cancelBtn) return;

        function setModalOpen(isOpen) {
            modal.classList.toggle('hidden-modal', !isOpen);
            document.body.style.overflow = isOpen ? 'hidden' : '';
        }

        openBtn.addEventListener('click', () => setModalOpen(true));
        closeBtn.addEventListener('click', () => setModalOpen(false));
        cancelBtn.addEventListener('click', () => setModalOpen(false));
        modal.addEventListener('click', (event) => {
            if (event.target === modal) setModalOpen(false);
        });
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
