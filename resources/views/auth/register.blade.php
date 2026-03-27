@extends('layouts.app', ['title' => 'Crear cuenta | Sillarri Climb'])

@section('content')
<section class="auth-wrap">
    <div class="auth-card">
        <h1>Únete a Sillarri</h1>
        <p>Crea tu perfil y empieza a registrar bloques, vías y entrenos.</p>

        <form method="POST" action="{{ route('register.store') }}" class="auth-form">
            @csrf

            <label>Nombre</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Username</label>
            <input type="text" name="username" value="{{ old('username') }}" required>
            @error('username')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Contraseña</label>
            <input type="password" name="password" required>
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Repite la contraseña</label>
            <input type="password" name="password_confirmation" required>

            <button type="submit" class="btn btn-primary btn-block">Crear cuenta</button>
        </form>

        <p class="switch-link">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
    </div>
</section>
@endsection
