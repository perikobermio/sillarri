@extends('layouts.app', ['title' => 'Estadisticas | Sillarri Climb'])

@section('content')
<section class="dashboard">
    <div class="panel">
        <p class="eyebrow">Perfil publico</p>
        <h1>{{ $userProfile->name }}</h1>
        <p>Resumen publico de actividad en bloques KILTER.</p>
    </div>

    <div class="stats-grid">
        <article>
            <h3>Bloques creados</h3>
            <p>{{ $totalBlocks }}</p>
        </article>
        <article>
            <h3>Mejor grado</h3>
            <p>{{ $bestGrade }}</p>
        </article>
        <article>
            <h3>Ultimo bloque</h3>
            <p>{{ $blocks->first()?->created_at?->format('Y-m-d') ?? '-' }}</p>
        </article>
    </div>
</section>
@endsection
