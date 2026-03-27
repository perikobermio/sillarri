@extends('layouts.app', ['title' => 'Sillarri Climb | Eskalada'])

@section('content')
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">Eskalada komunitatea</p>
        <h1>Igo gorago, entrenatu hobeto, partekatu bloke bakoitza.</h1>
        <p class="lead">
            Sillarri Climb-ek bideak, entrenamenduak eta sokakideak leku berean batzen ditu,
            haitza bizi duen jendearentzat.
        </p>

        <div class="cta-row">
            @auth
                <a class="btn btn-primary" href="{{ route('dashboard') }}">Nire panelera joan</a>
            @else
                <a class="btn btn-primary" href="{{ route('register') }}">Hasi doan</a>
                <a class="btn btn-secondary" href="{{ route('login') }}">Dagoeneko kontua dut</a>
            @endauth
        </div>
    </div>

    <div class="hero-card">
        <img src="https://images.unsplash.com/photo-1522163182402-834f871fd851?auto=format&fit=crop&w=1200&q=80" alt="Eskalatzailea harkaitz horman">
        <div class="card-overlay">
            <h2>Asteko bidea</h2>
            <p>"Aresta del Vent" · 6c · 38m · Kareharri teknikoa</p>
        </div>
    </div>
</section>

<a class="kilter-spotlight" href="{{ route('kilter') }}">
    <p class="eyebrow">Atal nagusia</p>
    <h2>KILTER</h2>
    <p>Plaka eta desplome saioak, ranking-a, entrenamendua eta asteko erronkak.</p>
    <span>KILTER atalera sartu</span>
</a>

<section class="feature-grid">
    <article class="feature">
        <h3>Aurrerapenaren jarraipena</h3>
        <p>Erregistratu zure bideak eta ikusi bilakaera gradu, estilo eta saioetan.</p>
    </article>
    <article class="feature">
        <h3>Entrenamendu egunkaria</h3>
        <p>Planifikatu campus, indarra eta erresistentzia metrika argiekin.</p>
    </article>
    <article class="feature">
        <h3>Tokiko komunitatea</h3>
        <p>Konektatu zure inguruko eskalatzaileekin eta antolatu irteerak.</p>
    </article>
</section>
@endsection
