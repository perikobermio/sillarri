<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sillarri Climb' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/climb.css') }}">
</head>
<body>
    <div class="bg-layer"></div>

    <header class="site-header">
        <a class="brand" href="{{ route('home') }}">Sillarri Climb</a>

        <nav class="nav-links">
            <a class="kilter-nav" href="{{ route('kilter') }}">KILTER</a>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <div class="user-chip">
                    <img src="{{ asset('images/default-avatar.svg') }}" alt="Foto de perfil por defecto">
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="icon-btn" title="Cerrar sesión" aria-label="Cerrar sesión">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M15.75 16.5 20.25 12 15.75 7.5M20 12H9.75M12 20.25H6.75A2.25 2.25 0 0 1 4.5 18V6A2.25 2.25 0 0 1 6.75 3.75H12" />
                        </svg>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}">Entrar</a>
                <a href="{{ route('register') }}">Crear cuenta</a>
            @endauth
        </nav>
    </header>

    <main>
        @if(session('status'))
            <div class="flash-ok">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
