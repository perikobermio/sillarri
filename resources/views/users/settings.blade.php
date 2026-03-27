@extends('layouts.app', ['title' => 'Settings | Sillarri Climb'])

@section('content')
<section class="dashboard">
    <div class="panel">
        <p class="eyebrow">Seccion privada</p>
        <h1>Settings</h1>
        <p>Gestion privada de la cuenta.</p>
    </div>

    <div class="panel">
        <h3>Datos de usuario</h3>
        <p><strong>Nombre:</strong> {{ $userProfile->name }}</p>
        <p><strong>Email:</strong> {{ $userProfile->email }}</p>
        <p><strong>Username:</strong> {{ $userProfile->username ?? '-' }}</p>
    </div>
</section>
@endsection
