@extends('layouts.app', ['title' => 'Dashboard | Sillarri Climb'])

@section('content')
<section class="dashboard">
    <div class="panel">
        <p class="eyebrow">Panel personal</p>
        <h1>Hola, {{ auth()->user()->name }}</h1>
        <p>
            Tu cuenta ya está activa. Desde aquí podrás añadir tu diario de escalada,
            registrar vías y preparar tus próximas salidas.
        </p>
    </div>

    <div class="stats-grid">
        <article>
            <h3>Bloques esta semana</h3>
            <p>12</p>
        </article>
        <article>
            <h3>Mejor grado del mes</h3>
            <p>7a</p>
        </article>
        <article>
            <h3>Horas de entreno</h3>
            <p>9h 40m</p>
        </article>
    </div>
</section>
@endsection
