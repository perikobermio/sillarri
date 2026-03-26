@extends('layouts.app', ['title' => 'KILTER | Sillarri Climb'])

@section('content')
<section class="kilter-page">
    <div class="kilter-table-wrap">
        <div class="kilter-table-head">
            <div>
                <p class="eyebrow">Kilter Board Hub</p>
                <h1>KILTER BLOQUES</h1>
            </div>

            <div class="kilter-actions">
                @auth
                    <a class="btn btn-primary" href="{{ route('kilter.create') }}">Crear bloque</a>
                @endauth

                <form method="GET" action="{{ route('kilter') }}" class="kilter-search">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Buscar bloque por nombre..."
                    >
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    @if($search !== '')
                        <a class="btn btn-secondary" href="{{ route('kilter') }}">Limpiar</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-scroll">
            <table class="kilter-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Grado</th>
                        <th>Map ID</th>
                        <th>Mapa</th>
                        <th>Ruta imagen mapa</th>
                        <th>Creador</th>
                        <th>Boulder</th>
                        <th>Creado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blocks as $block)
                        <tr>
                            <td>{{ $block->id }}</td>
                            <td>{{ $block->name }}</td>
                            <td>{{ $block->description }}</td>
                            <td>{{ $block->grade }}</td>
                            <td>{{ $block->map_id }}</td>
                            <td>{{ $block->map?->name ?? '-' }}</td>
                            <td><code>{{ $block->map?->image_physical_path ?? '-' }}</code></td>
                            <td>{{ $block->creator?->name ?? '-' }}</td>
                            <td><code>{{ $block->boulder }}</code></td>
                            <td>{{ $block->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">No hay bloques para el filtro actual.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
