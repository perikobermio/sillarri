@extends('layouts.app', ['title' => 'Sillarri Climb | Escalada'])

@section('content')
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">Comunidad de escalada</p>
        <h1>Escala más alto, entrena mejor, comparte cada bloque.</h1>
        <p class="lead">
            Sillarri Climb une rutas, entrenamientos y compañeros de cordada en un espacio diseñado para
            gente que vive la roca.
        </p>

        <div class="cta-row">
            @auth
                <a class="btn btn-primary" href="{{ route('dashboard') }}">Ir a mi panel</a>
            @else
                <a class="btn btn-primary" href="{{ route('register') }}">Empieza gratis</a>
                <a class="btn btn-secondary" href="{{ route('login') }}">Ya tengo cuenta</a>
            @endauth
        </div>
    </div>

    <div class="hero-card">
        <img src="https://images.unsplash.com/photo-1522163182402-834f871fd851?auto=format&fit=crop&w=1200&q=80" alt="Escalador en pared de roca">
        <div class="card-overlay">
            <h2>Ruta de la semana</h2>
            <p>"Aresta del Vent" · 6c · 38m · Caliza técnica</p>
        </div>
    </div>
</section>

<a class="kilter-spotlight" href="{{ route('kilter') }}">
    <p class="eyebrow">Seccion principal</p>
    <h2>KILTER</h2>
    <p>Entradas de placa, desplome, ranking, entrenamiento y retos semanales.</p>
    <span>Entrar al apartado KILTER</span>
</a>

<section class="feature-grid">
    <article class="feature">
        <h3>Seguimiento de progreso</h3>
        <p>Registra tus vías y ve tu evolución por grado, estilo y sesiones.</p>
    </article>
    <article class="feature">
        <h3>Diario de entreno</h3>
        <p>Planifica campus, fuerza y resistencia con métricas claras.</p>
    </article>
    <article class="feature">
        <h3>Comunidad local</h3>
        <p>Conecta con escaladores de tu zona y organiza salidas en roca.</p>
    </article>
</section>
@endsection
