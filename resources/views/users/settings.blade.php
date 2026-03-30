@extends('layouts.app', ['title' => 'Ezarpenak | Sillarri Climb'])

@section('content')
<section class="dashboard">
    <div class="panel">
        <p class="eyebrow">Atal pribatua</p>
        <h1>Ezarpenak</h1>
        <p>Editatu zure kontuaren datuak.</p>
    </div>

    <div class="panel">
        <h3>Kontuaren datuak</h3>

        <form method="POST" action="{{ route('settings.update') }}" class="auth-form">
            @csrf
            @method('PUT')

            <label>Izena</label>
            <input type="text" name="name" value="{{ old('name', $userProfile->name) }}" required>
            @error('name')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $userProfile->email) }}" required>
            @error('email')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Username</label>
            <input type="text" value="{{ $userProfile->username ?? '-' }}" disabled>

            <label>Pasahitz berria (aukerakoa)</label>
            <input type="password" name="password">
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Errepikatu pasahitz berria</label>
            <input type="password" name="password_confirmation">

            <button type="submit" class="btn btn-primary">Gorde aldaketak</button>
        </form>
    </div>
</section>
@endsection
