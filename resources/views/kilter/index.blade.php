@extends('layouts.app', ['title' => 'KILTER | Sillarri Climb'])

@section('content')
<section class="kilter-page">
    <div class="kilter-hero">
        <p class="eyebrow">Apartado KILTER</p>
        <h1>KILTER BOARD HUB</h1>
        <p>
            Bienvenido al espacio principal de KILTER. Aquí podrás registrar sesiones,
            ver rankings por grado y seguir retos de bloque en tiempo real.
        </p>
        <a class="btn btn-primary" href="{{ route('home') }}">Volver al inicio</a>
    </div>

    <div class="kilter-grid">
        <article>
            <h3>Sesion rapida</h3>
            <p>Guarda bloques encadenados, pegues y nivel de esfuerzo.</p>
        </article>
        <article>
            <h3>Top semanal</h3>
            <p>Consulta quién está encadenando más duro esta semana.</p>
        </article>
        <article>
            <h3>Retos KILTER</h3>
            <p>Objetivos progresivos para subir de grado con método.</p>
        </article>
    </div>
</section>
@endsection
