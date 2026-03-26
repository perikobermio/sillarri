# Sillarri Climb

Aplicación web de escalada con:

- Backend `Laravel 13`
- BBDD `PostgreSQL`
- Stack completo en Docker (`app` + `nginx` + `postgres`)
- Login, registro y dashboard privado
- Landing moderna inspirada en el mundo de la escalada

## Requisitos

- Docker
- Docker Compose

## Arranque local

1. Copia variables de entorno:

```bash
cp .env.example .env
```

2. Levanta servicios:

```bash
docker compose up -d --build
```

3. Instala dependencias y genera clave dentro del contenedor `app`:

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

4. Ejecuta migraciones:

```bash
docker compose exec app php artisan migrate
```

5. Abre la app en:

```text
http://TU_IP_NAS:8080
```

## Servicios Docker

- `app`: PHP-FPM (Laravel)
- `web`: Nginx (expone puerto `8080`)
- `db`: PostgreSQL (`sillarri` / `sillarri` / `sillarri_pass`)

## Reverse Proxy con Caddy (NAS)

Ejemplo de `Caddyfile` en el host que hace de reverse proxy:

```caddy
escalada.tudominio.com {
    reverse_proxy 127.0.0.1:8080
}
```

Si Caddy está en otra máquina, cambia `127.0.0.1` por la IP de tu NAS donde corre esta app.

## Rutas principales

- `/` inicio pública
- `/login` iniciar sesión
- `/register` crear cuenta
- `/dashboard` zona privada (requiere auth)
- `POST /logout` cerrar sesión

## Notas

- El CSS está en `public/css/climb.css`.
- Las vistas Blade están en `resources/views`.
- El proyecto usa sesiones en base de datos (`SESSION_DRIVER=database`).
