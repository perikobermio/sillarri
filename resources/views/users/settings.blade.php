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

        @php
            $avatarPath = $userProfile->avatar_path ?? '';
            $avatarUrl = $avatarPath !== ''
                ? (\Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])
                    ? $avatarPath
                    : '/storage/'.$avatarPath)
                : '/images/default-avatar.svg';
        @endphp

        <form method="POST" action="{{ route('settings.update') }}" class="auth-form" enctype="multipart/form-data">
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

            <label>Avatarra</label>
            <div class="avatar-field">
                <img class="avatar-preview" src="{{ $avatarUrl }}" alt="Avatarra">
                <input type="file" name="avatar" accept="image/*">
            </div>
            @error('avatar')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Pasahitz berria (aukerakoa)</label>
            <input type="password" name="password">
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Errepikatu pasahitz berria</label>
            <input type="password" name="password_confirmation">

            @if((bool) $userProfile->is_admin)
                <hr class="detail-separator">
                <h4>Administratzaileen ezarpenak</h4>

                <label>Blokeen zerrendako orriko kopurua</label>
                <input
                    type="number"
                    name="kilter_blocks_per_page"
                    min="2"
                    max="100"
                    value="{{ old('kilter_blocks_per_page', $blockListPageSize ?? 50) }}"
                    required
                >
                @error('kilter_blocks_per_page')
                    <small class="error">{{ $message }}</small>
                @enderror
            @endif

            <button type="submit" class="btn btn-primary">Gorde aldaketak</button>
        </form>
    </div>
</section>
@endsection
