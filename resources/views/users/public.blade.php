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
</section>
@endsection
