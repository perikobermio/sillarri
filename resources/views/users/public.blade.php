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

    <div class="panel user-public-completions">
        <h3>Egindako blokeak</h3>
        @if($completedBlocks->isEmpty())
            <p>Oraindik ez du blokerik eginda markatu.</p>
        @else
            <div class="table-scroll user-public-table-wrap">
                <table class="kilter-table user-public-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Izena</th>
                            <th>Gradua</th>
                            <th>Mapa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedBlocks as $block)
                            <tr>
                                <td>{{ $block->id }}</td>
                                <td>
                                    <a href="{{ route('kilter.show', $block) }}">{{ $block->name }}</a>
                                </td>
                                <td>{{ $block->grade }}</td>
                                <td>{{ $block->map?->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="panel user-public-completions">
        <h3>Sortutako blokeak</h3>
        @if($createdBlocks->isEmpty())
            <p>Ez du blokerik sortu oraindik.</p>
        @else
            <div class="table-scroll user-public-table-wrap">
                <table class="kilter-table user-public-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Izena</th>
                            <th>Gradua</th>
                            <th>Mapa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($createdBlocks as $block)
                            <tr>
                                <td>{{ $block->id }}</td>
                                <td>
                                    <a href="{{ route('kilter.show', $block) }}">{{ $block->name }}</a>
                                </td>
                                <td>{{ $block->grade }}</td>
                                <td>{{ $block->map?->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
@endsection
