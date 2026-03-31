@extends('layouts.app', ['title' => 'Admin | Sillarri Climb'])

@section('content')
<section class="dashboard admin-page">
    <div class="panel">
        <p class="eyebrow">Administrazioa</p>
        <h1>Admin panela</h1>
        <p>Erabiltzaileak, mapak eta eguraldi guneak kudeatu.</p>
    </div>

    <div class="panel admin-section">
        <div class="admin-section-head">
            <h3>Erabiltzaileak</h3>
            <button type="button" class="btn btn-primary btn-icon" id="open-user-create" aria-label="Erabiltzaile berria" title="Erabiltzaile berria">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            </button>
        </div>
        <div class="table-scroll">
            <table class="kilter-table admin-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Ekintzak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->username }}</td>
                            <td class="admin-actions">
                                <button
                                    type="button"
                                    class="btn btn-secondary btn-icon admin-edit-user"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-username="{{ $user->username }}"
                                    data-email="{{ $user->email }}"
                                    data-admin="{{ $user->is_admin ? '1' : '0' }}"
                                    aria-label="Editatu"
                                    title="Editatu"
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm14.71-9.04a1.003 1.003 0 0 0 0-1.42l-2.5-2.5a1.003 1.003 0 0 0-1.42 0l-1.96 1.96 3.75 3.75 1.96-1.79z"/></svg>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-danger btn-icon admin-delete-user"
                                    data-action="{{ route('admin.users.delete', $user) }}"
                                    data-label="Erabiltzailea"
                                    aria-label="Ezabatu"
                                    title="Ezabatu"
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel admin-section">
        <h3>Mapak</h3>
        <div class="table-scroll">
            <table class="kilter-table admin-table">
                <thead>
                    <tr>
                        <th>Izena</th>
                        <th>Miniatura</th>
                        <th>Ekintzak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maps as $map)
                        @php
                            $mapUrl = \Illuminate\Support\Str::startsWith($map->image, ['http://', 'https://', '/'])
                                ? $map->image
                                : '/storage/'.$map->image;
                        @endphp
                        <tr>
                            <td>{{ $map->name }}</td>
                            <td><img class="admin-map-thumb" src="{{ $mapUrl }}" alt="{{ $map->name }}"></td>
                            <td class="admin-actions">
                                <button
                                    type="button"
                                    class="btn btn-danger btn-icon admin-delete-map"
                                    data-action="{{ route('admin.maps.delete', $map) }}"
                                    data-label="Mapa"
                                    aria-label="Ezabatu"
                                    title="Ezabatu"
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel admin-section">
        <div class="admin-section-head">
            <h3>Aurreikuspen meteorologikoa</h3>
            <button type="button" class="btn btn-primary btn-icon" id="open-location-create" aria-label="Herria gehitu" title="Herria gehitu">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            </button>
        </div>

        <div class="table-scroll">
            <table class="kilter-table admin-table">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Ekintzak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($locations as $location)
                        <tr>
                            <td>{{ $location->label ?? $location->name }}</td>
                            <td class="admin-actions">
                                <button
                                    type="button"
                                    class="btn btn-danger btn-icon admin-delete-location"
                                    data-action="{{ route('admin.locations.delete', $location) }}"
                                    data-label="Herria"
                                    aria-label="Ezabatu"
                                    title="Ezabatu"
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="modal-shell hidden-modal" id="admin-user-create-modal" role="dialog" aria-modal="true" aria-labelledby="admin-user-create-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="admin-user-create-title">Erabiltzaile berria</h2>
            <button type="button" class="icon-btn" id="close-user-create">×</button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" class="auth-form">
            @csrf
            <label>Izena</label>
            <input type="text" name="name" required>
            <label>Username</label>
            <input type="text" name="username" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Pasahitza</label>
            <input type="password" name="password" required>
            <label class="check-row">
                <input type="checkbox" name="is_admin" value="1">
                <span>Admin</span>
            </label>
            <button type="submit" class="btn btn-primary">Sortu</button>
        </form>
    </div>
</div>

<div class="modal-shell hidden-modal" id="admin-user-edit-modal" role="dialog" aria-modal="true" aria-labelledby="admin-user-edit-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="admin-user-edit-title">Erabiltzailea editatu</h2>
            <button type="button" class="icon-btn" id="close-user-edit">×</button>
        </div>
        <form method="POST" action="#" class="auth-form" id="admin-user-edit-form">
            @csrf
            @method('PUT')
            <label>Izena</label>
            <input type="text" name="name" id="admin-edit-name" required>
            <label>Username</label>
            <input type="text" name="username" id="admin-edit-username" required>
            <label>Email</label>
            <input type="email" name="email" id="admin-edit-email" required>
            <label>Pasahitz berria (aukerakoa)</label>
            <input type="password" name="password">
            <label class="check-row">
                <input type="checkbox" name="is_admin" value="1" id="admin-edit-admin">
                <span>Admin</span>
            </label>
            <button type="submit" class="btn btn-primary">Gorde</button>
        </form>
    </div>
</div>

<div class="modal-shell hidden-modal" id="admin-location-create-modal" role="dialog" aria-modal="true" aria-labelledby="admin-location-create-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="admin-location-create-title">Herria gehitu</h2>
            <button type="button" class="icon-btn" id="close-location-create">×</button>
        </div>
        <form method="POST" action="{{ route('admin.locations.store') }}" class="auth-form">
            @csrf
            <label>Bilaketa izena</label>
            <input type="text" name="name" placeholder="Adib: Gernika" required>
            <label>Label (front)</label>
            <input type="text" name="label" placeholder="Adib: Gernika" required>
            <button type="submit" class="btn btn-primary">Gehitu</button>
        </form>
    </div>
</div>

<div class="modal-shell hidden-modal" id="admin-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="admin-confirm-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="admin-confirm-title">Berretsi</h2>
            <button type="button" class="icon-btn" id="close-admin-confirm">×</button>
        </div>
        <p id="admin-confirm-text">Ziur zaude?</p>
        <form method="POST" action="#" id="admin-confirm-form" class="admin-confirm-actions">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-secondary" id="admin-confirm-cancel">Utzi</button>
            <button type="submit" class="btn btn-danger">Ezabatu</button>
        </form>
    </div>
</div>

<script>
    (function () {
        const createModal = document.getElementById('admin-user-create-modal');
        const editModal = document.getElementById('admin-user-edit-modal');
        const confirmModal = document.getElementById('admin-confirm-modal');
        const openCreate = document.getElementById('open-user-create');
        const closeCreate = document.getElementById('close-user-create');
        const closeEdit = document.getElementById('close-user-edit');
        const closeConfirm = document.getElementById('close-admin-confirm');
        const cancelConfirm = document.getElementById('admin-confirm-cancel');
        const confirmForm = document.getElementById('admin-confirm-form');
        const confirmText = document.getElementById('admin-confirm-text');
        const locationCreateModal = document.getElementById('admin-location-create-modal');
        const openLocationCreate = document.getElementById('open-location-create');
        const closeLocationCreate = document.getElementById('close-location-create');

        const editForm = document.getElementById('admin-user-edit-form');
        const editName = document.getElementById('admin-edit-name');
        const editUsername = document.getElementById('admin-edit-username');
        const editEmail = document.getElementById('admin-edit-email');
        const editAdmin = document.getElementById('admin-edit-admin');

        function openModal(modal) {
            modal?.classList.remove('hidden-modal');
        }

        function closeModal(modal) {
            modal?.classList.add('hidden-modal');
        }

        openCreate?.addEventListener('click', () => openModal(createModal));
        openLocationCreate?.addEventListener('click', () => openModal(locationCreateModal));
        closeCreate?.addEventListener('click', () => closeModal(createModal));
        closeEdit?.addEventListener('click', () => closeModal(editModal));
        closeLocationCreate?.addEventListener('click', () => closeModal(locationCreateModal));
        closeConfirm?.addEventListener('click', () => closeModal(confirmModal));
        cancelConfirm?.addEventListener('click', () => closeModal(confirmModal));

        document.querySelectorAll('.admin-edit-user').forEach((button) => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                editForm.action = `/admin/users/${id}`;
                editName.value = button.dataset.name || '';
                editUsername.value = button.dataset.username || '';
                editEmail.value = button.dataset.email || '';
                editAdmin.checked = button.dataset.admin === '1';
                openModal(editModal);
            });
        });

        document.querySelectorAll('.admin-delete-user, .admin-delete-map, .admin-delete-location').forEach((button) => {
            button.addEventListener('click', () => {
                const action = button.dataset.action;
                const label = button.dataset.label || 'Elementua';
                confirmText.textContent = `${label} ezabatu nahi duzu?`;
                confirmForm.action = action;
                openModal(confirmModal);
            });
        });

        [createModal, editModal, confirmModal, locationCreateModal].forEach((modal) => {
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) closeModal(modal);
            });
        });
    })();
</script>
@endsection
