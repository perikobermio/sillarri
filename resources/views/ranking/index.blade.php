@extends('layouts.app', ['title' => 'Sailkapena | Sillarri Climb'])

@section('content')
<section class="kilter-page">
    <div class="kilter-table-wrap">
        <div class="kilter-table-head">
            <div>
                <p class="eyebrow">Sillarri League</p>
                <h1>HALL OF SHAME</h1>
            </div>
        </div>

        <div class="ranking-table-wrap">
            <table class="kilter-table ranking-table">
                <colgroup>
                    <col class="ranking-col-rank">
                    <col class="ranking-col-user">
                    <col class="ranking-col-score">
                </colgroup>
                <thead>
                    <tr>
                        <th class="ranking-col-rank" aria-label="Posizioa"></th>
                        <th>Erabiltzailea</th>
                        <th>
                            <span class="ranking-score-head">
                                <span>Puntuazioa</span>
                                <button
                                    type="button"
                                    class="icon-btn ranking-info-btn"
                                    id="open-ranking-scale"
                                    aria-label="Puntuazio baremoa ikusi"
                                    title="Puntuazio baremoa"
                                >
                                    i
                                </button>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ranking as $i => $row)
                        <tr>
                            <td><strong>{{ (($ranking->currentPage() - 1) * $ranking->perPage()) + $i + 1 }}</strong></td>
                            <td>
                                <a class="ranking-user-link" href="{{ route('users.public', ['user' => $row['user_id']]) }}">
                                    {{ $row['username'] }}
                                </a>
                            </td>
                            <td><strong>{{ number_format($row['score'], 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Oraindik ez dago daturik sailkapenerako.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ranking->hasPages())
            <div class="kilter-pagination" role="navigation" aria-label="Sailkapenaren orriak">
                <div class="pagination-summary">
                    Orria {{ $ranking->currentPage() }} / {{ $ranking->lastPage() }}
                </div>
                <div class="pagination-actions">
                    @if($ranking->onFirstPage())
                        <span class="pagination-btn is-disabled">Aurrekoa</span>
                    @else
                        <a class="pagination-btn" href="{{ $ranking->previousPageUrl() }}">Aurrekoa</a>
                    @endif

                    @if($ranking->hasMorePages())
                        <a class="pagination-btn" href="{{ $ranking->nextPageUrl() }}">Hurrengoa</a>
                    @else
                        <span class="pagination-btn is-disabled">Hurrengoa</span>
                    @endif
                </div>
            </div>
        @endif

        <div class="ranking-highlight-grid">
            @foreach($highlights as $highlight)
                @php
                    $user = $highlight['user'];
                    $value = $highlight['format']($user);
                @endphp
                <article class="ranking-highlight-card">
                    <p class="ranking-highlight-label">{{ $highlight['title'] }}</p>
                    @if($user)
                        <a class="ranking-user-link ranking-highlight-user" href="{{ route('users.public', ['user' => $user['user_id']]) }}">
                            {{ $user['username'] }}
                        </a>
                    @else
                        <p class="ranking-highlight-user">-</p>
                    @endif
                    <p class="ranking-highlight-value">{{ $value }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<div class="modal-shell hidden-modal" id="ranking-scale-modal" role="dialog" aria-modal="true" aria-labelledby="ranking-scale-title">
    <div class="modal-card ranking-scale-modal-card">
        <div class="modal-head">
            <h2 id="ranking-scale-title">Puntuazio baremoa</h2>
            <button type="button" class="icon-btn" id="close-ranking-scale" aria-label="Itxi leihoa">×</button>
        </div>
        <div class="ranking-scale-grid">
            @foreach($scoreTable as $grade => $points)
                <div class="ranking-scale-row">
                    <span class="ranking-scale-grade">{{ strtoupper($grade) }}</span>
                    <span class="ranking-scale-points">{{ number_format($points, 0) }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('ranking-scale-modal');
        const openBtn = document.getElementById('open-ranking-scale');
        const closeBtn = document.getElementById('close-ranking-scale');

        if (!modal || !openBtn || !closeBtn) return;

        function syncBodyScrollLock() {
            const hasOpenModal = document.querySelector('.modal-shell:not(.hidden-modal)') !== null;
            document.body.style.overflow = hasOpenModal ? 'hidden' : '';
        }

        function openModal() {
            modal.classList.remove('hidden-modal');
            syncBodyScrollLock();
        }

        function closeModal() {
            modal.classList.add('hidden-modal');
            syncBodyScrollLock();
        }

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
    })();
</script>
@endsection
