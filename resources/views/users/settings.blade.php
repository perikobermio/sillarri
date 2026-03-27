@extends('layouts.app', ['title' => 'Ezarpenak | Sillarri Climb'])

@section('content')
<section class="dashboard">
    <div class="panel">
        <p class="eyebrow">Atal pribatua</p>
        <h1>Ezarpenak</h1>
        <p>Kontuaren kudeaketa pribatua.</p>
    </div>

    <div class="panel">
        <h3>Erabiltzaile datuak</h3>
        <p><strong>Izena:</strong> {{ $userProfile->name }}</p>
        <p><strong>Email:</strong> {{ $userProfile->email }}</p>
        <p><strong>Erabiltzaile-izena:</strong> {{ $userProfile->username ?? '-' }}</p>
    </div>
</section>
@endsection
