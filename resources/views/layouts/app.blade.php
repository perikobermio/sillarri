<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sillarri Climb' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/climb.css">
</head>
<body>
    <div class="bg-layer"></div>

    <header class="site-header">
        <a class="brand" href="{{ route('home') }}">Sillarri Climb</a>

        <nav class="nav-links">
            <a class="kilter-nav" href="{{ route('kilter') }}">KILTER</a>
            @auth
                <details class="user-menu">
                    <summary class="user-chip">
                        <img src="/images/default-avatar.svg" alt="Foto de perfil por defecto">
                        <span>{{ auth()->user()->username ?? auth()->user()->name }}</span>
                    </summary>
                    <div class="user-dropdown">
                        @if(\Illuminate\Support\Facades\Route::has('users.public'))
                            <a href="{{ route('users.public', auth()->user()) }}">Estadisticas</a>
                        @endif
                        <a href="{{ route('settings') }}">Settings</a>
                        <hr>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </details>
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

    <script>
        (function () {
            const menus = document.querySelectorAll('.user-menu');

            if (!menus.length) return;

            document.addEventListener('click', (event) => {
                menus.forEach((menu) => {
                    if (!menu.open) return;
                    if (menu.contains(event.target)) return;
                    menu.open = false;
                });
            });
        })();
    </script>
</body>
</html>
