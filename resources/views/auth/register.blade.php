@extends('layouts.app', ['title' => 'Kontua sortu | Sillarri Climb'])

@section('content')
<section class="auth-wrap">
    <div class="auth-card">
        <h1>Bat egin Sillarri-rekin</h1>
        <p>Sortu zure profila eta hasi blokeak, bideak eta entrenamenduak erregistratzen.</p>

        <form method="POST" action="{{ route('register.store') }}" class="auth-form">
            @csrf

            <label>Izena</label>
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

            <label>Pasahitza</label>
            <input type="password" name="password" required>
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Errepikatu pasahitza</label>
            <input type="password" name="password_confirmation" required>

            <button type="submit" class="btn btn-primary btn-block">Kontua sortu</button>
        </form>

        <p class="switch-link">Kontua baduzu? <a href="{{ route('login') }}">Hasi saioa</a></p>
    </div>
</section>
@endsection
