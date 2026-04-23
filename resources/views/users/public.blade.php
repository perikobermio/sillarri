@extends('layouts.app', ['title' => 'Estatistikak | Sillarri Climb'])

@section('content')
<section class="dashboard">
    <div class="panel user-public-hero">
        <p class="eyebrow">Profil publikoa</p>
        @php
            $avatarPath = $userProfile->avatar_path ?? '';
            $avatarUrl = $avatarPath !== ''
                ? (\Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])
                    ? $avatarPath
                    : '/storage/'.$avatarPath)
                : '/images/default-avatar.svg';
        @endphp
        <div class="public-profile-head">
            <img class="public-avatar" src="{{ $avatarUrl }}" alt="Erabiltzailearen avatarra">
            <h1>{{ $userProfile->username ?? $userProfile->name }}</h1>
        </div>
        <p>KILTER jardueraren laburpena: egindako blokeak, zailtasuna eta datu nagusiak.</p>
    </div>

    @if($isOwnProfile && $pendingRecotes->isNotEmpty())
        <div class="panel user-public-completions">
            <h3>Rekotazioak</h3>
            <div class="table-scroll user-public-table-wrap">
                <table class="kilter-table user-public-table">
                    <thead>
                        <tr>
                            <th class="recote-col-name">Izena</th>
                            <th class="recote-col-grade">Prop.</th>
                            <th class="recote-col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRecotes as $recote)
                            <tr>
                                <td class="recote-col-name">
                                    <a href="{{ route('kilter.show', $recote['block']) }}">{{ $recote['block']->name }}</a>
                                </td>
                                <td class="recote-col-grade">{{ $recote['current_grade'] }} → {{ $recote['suggested_grade'] ?? '-' }}</td>
                                <td class="recote-col-actions">
                                    <div class="recote-actions">
                                        <form method="POST" action="{{ route('kilter.recote.resolve', $recote['block']) }}" class="detail-action-form">
                                            @csrf
                                            <input type="hidden" name="decision" value="accept">
                                            <button type="submit" class="btn btn-primary recote-action-btn recote-mini-btn recote-action-ghost" title="Onartu gradua" aria-label="Onartu gradua">
                                                <span class="action-icon" aria-hidden="true">
                                                    <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                                                        <path d="M5 12l4 4 10-10"></path>
                                                    </svg>
                                                </span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('kilter.recote.resolve', $recote['block']) }}" class="detail-action-form">
                                            @csrf
                                            <input type="hidden" name="decision" value="reject">
                                            <button type="submit" class="btn btn-secondary recote-action-btn recote-mini-btn recote-action-ghost" title="Baztertu rekotazioak" aria-label="Baztertu rekotazioak">
                                                <span class="action-icon" aria-hidden="true">
                                                    <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                                                        <path d="M6 6l12 12"></path>
                                                        <path d="M18 6l-12 12"></path>
                                                    </svg>
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="stats-grid">
        <article>
            <h3>Egindako blokeak</h3>
            <p>{{ $totalCompletedBlocks }}</p>
        </article>
        <article>
            <h3>Gradu onena</h3>
            <p>{{ $bestGrade }}</p>
        </article>
        <article>
            <h3>Zailtasun %</h3>
            <p>{{ number_format($difficultyPercent, 1) }}%</p>
        </article>
    </div>

    @include('users.partials.public-block-list', [
        'title' => 'Egindako blokeak',
        'blocks' => $completedBlocks,
        'emptyText' => 'Oraindik ez du blokerik eginda markatu.',
        'panelId' => 'completed-blocks-panel',
    ])

    @include('users.partials.public-block-list', [
        'title' => 'Sortutako blokeak',
        'blocks' => $createdBlocks,
        'emptyText' => 'Ez du blokerik sortu oraindik.',
        'panelId' => 'created-blocks-panel',
    ])
</section>

<script>
    (function () {
        const panels = document.querySelectorAll('[data-public-block-panel]');
        if (panels.length === 0) return;

        async function loadPanel(url, panel) {
            const section = panel.id === 'completed-blocks-panel' ? 'completed' : 'created';
            const requestUrl = new URL(url, window.location.origin);
            requestUrl.searchParams.set('section', section);

            panel.classList.add('is-loading');

            try {
                const response = await fetch(requestUrl.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    window.location.href = url;
                    return;
                }

                panel.outerHTML = await response.text();
            } catch (error) {
                window.location.href = url;
            } finally {
                panel.classList.remove('is-loading');
            }
        }

        document.addEventListener('click', (event) => {
            const link = event.target.closest('.user-public-pagination-link');
            if (!link) return;

            const panel = link.closest('[data-public-block-panel]');
            if (!panel) return;

            event.preventDefault();
            loadPanel(link.href, panel);
        });
    })();
</script>
@endsection
