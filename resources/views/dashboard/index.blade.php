@extends('layouts.app', ['title' => 'Dashboard | Sillarri Climb'])

@section('content')
<section class="dashboard">
    <div class="panel">
        <p class="eyebrow">Panel pertsonala</p>
        <h1>Kaixo, {{ auth()->user()->username ?? auth()->user()->name }}</h1>
        <p>
            Zure kontua aktibo dago. Hemendik zure eskalada egunkaria gehitu,
            bideak erregistratu eta hurrengo irteerak presta ditzakezu.
        </p>
    </div>

    <div class="stats-grid">
        <article>
            <h3>Aste honetako blokeak</h3>
            <p>12</p>
        </article>
        <article>
            <h3>Hilabeteko gradu onena</h3>
            <p>7a</p>
        </article>
        <article>
            <h3>Entrenamendu orduak</h3>
            <p>9h 40m</p>
        </article>
    </div>
</section>
@endsection
