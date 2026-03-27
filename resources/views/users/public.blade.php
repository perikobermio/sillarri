@extends('layouts.app', ['title' => 'Estatistikak | Sillarri Climb'])

@section('content')
<section class="dashboard">
    <div class="panel">
        <p class="eyebrow">Profil publikoa</p>
        <h1>{{ $userProfile->username ?? $userProfile->name }}</h1>
        <p>KILTER blokeei buruzko jarduera publikoaren laburpena.</p>
    </div>

    <div class="stats-grid">
        <article>
            <h3>Sortutako blokeak</h3>
            <p>{{ $totalBlocks }}</p>
        </article>
        <article>
            <h3>Gradu onena</h3>
            <p>{{ $bestGrade }}</p>
        </article>
        <article>
            <h3>Azken blokea</h3>
            <p>{{ $blocks->first()?->created_at?->format('Y-m-d') ?? '-' }}</p>
        </article>
    </div>
</section>
@endsection
