@extends('layouts.app', ['title' => 'Entrar | Sillarri Climb'])

@section('content')
<section class="auth-wrap">
    <div class="auth-card">
        <h1>Bienvenido de vuelta</h1>
        <p>Accede para seguir tus entrenamientos y próximas rutas.</p>

        <form method="POST" action="{{ route('login.attempt') }}" class="auth-form">
            @csrf

            <label>Email o usuario</label>
            <input type="text" name="login" value="{{ old('login') }}" required autofocus>
            @error('login')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Contraseña</label>
            <input type="password" name="password" required>
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror

            <label class="check-row">
                <input type="checkbox" name="remember">
                <span>Recordarme en este dispositivo</span>
            </label>

            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>

        <p class="switch-link">¿No tienes cuenta? <a href="{{ route('register') }}">Crea una ahora</a></p>
    </div>
</section>
@endsection
