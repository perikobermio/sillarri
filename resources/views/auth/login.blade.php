@extends('layouts.app', ['title' => 'Sartu | Sillarri Climb'])

@section('content')
<section class="auth-wrap">
    <div class="auth-card">
        <h1>Ongi etorri berriro</h1>
        <p>Sartu zure entrenamenduak eta hurrengo ibilbideak jarraitzeko.</p>

        <form method="POST" action="{{ route('login.attempt') }}" class="auth-form">
            @csrf

            <label>Emaila edo erabiltzaile-izena</label>
            <input type="text" name="login" value="{{ old('login') }}" required autofocus>
            @error('login')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Pasahitza</label>
            <input type="password" name="password" required>
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror

            <label class="check-row">
                <input type="checkbox" name="remember">
                <span>Gogoratu gailu honetan</span>
            </label>

            <button type="submit" class="btn btn-primary btn-block">Sartu</button>
        </form>

        <p class="switch-link">Ez duzu konturik? <a href="{{ route('register') }}">Sortu orain</a></p>
    </div>
</section>
@endsection
