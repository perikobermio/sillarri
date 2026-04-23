@extends('layouts.app', ['title' => 'Admin | Sillarri Climb'])

@section('content')
<section class="dashboard admin-page">
    <div class="panel">
        <p class="eyebrow">Administrazioa</p>
        <h1>Admin panela</h1>
        <p>Erabiltzaileak, mapak eta eguraldi guneak kudeatu.</p>
    </div>

    <div class="admin-tabs" data-admin-tabs>
        <div class="admin-tab-list" role="tablist" aria-label="Admin atalak">
            <button type="button" class="admin-tab is-active" role="tab" id="admin-tab-settings" aria-selected="true" aria-controls="admin-panel-settings" data-tab-target="settings">
                Parametroak
            </button>
            <button type="button" class="admin-tab" role="tab" id="admin-tab-users" aria-selected="false" aria-controls="admin-panel-users" data-tab-target="users" tabindex="-1">
                Erabiltzaileak
            </button>
            <button type="button" class="admin-tab" role="tab" id="admin-tab-maps" aria-selected="false" aria-controls="admin-panel-maps" data-tab-target="maps" tabindex="-1">
                Mapak
            </button>
            <button type="button" class="admin-tab" role="tab" id="admin-tab-weather" aria-selected="false" aria-controls="admin-panel-weather" data-tab-target="weather" tabindex="-1">
                Eguraldia
            </button>
            <button type="button" class="admin-tab" role="tab" id="admin-tab-orders" aria-selected="false" aria-controls="admin-panel-orders" data-tab-target="orders" tabindex="-1">
                Eskariak
            </button>
        </div>

        <div class="admin-tab-panels">
            <section class="panel admin-section admin-tab-panel is-active" role="tabpanel" id="admin-panel-settings" aria-labelledby="admin-tab-settings" data-tab-panel="settings">
                <h3>Parametroak</h3>
                <form method="POST" action="{{ route('admin.settings.update') }}" class="auth-form">
                    @csrf
                    @method('PUT')
                    <label>Blokeen zerrendako orriko kopurua</label>
                    <div class="admin-inline-row">
                        <input
                            type="number"
                            name="kilter_blocks_per_page"
                            min="2"
                            max="100"
                            value="{{ old('kilter_blocks_per_page', $blockListPageSize ?? 50) }}"
                            required
                        >
                        <button type="submit" class="btn btn-primary btn-icon" aria-label="Gorde" title="Gorde">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12l4 4L19 6"/></svg>
                        </button>
                    </div>
                    @error('kilter_blocks_per_page')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </form>
            </section>

            <section class="panel admin-section admin-tab-panel" role="tabpanel" id="admin-panel-users" aria-labelledby="admin-tab-users" data-tab-panel="users" hidden>
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
                                    <td><a class="user-profile-link" href="{{ route('users.public', $user) }}">{{ $user->username }}</a></td>
                                    <td class="admin-actions">
                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-icon admin-edit-user"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-username="{{ $user->username }}"
                                            data-email="{{ $user->email }}"
                                            data-avatar="{{ $user->avatar_path ? (\Illuminate\Support\Str::startsWith($user->avatar_path, ['http://', 'https://', '/']) ? $user->avatar_path : '/storage/'.$user->avatar_path) : '' }}"
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
            </section>

            <section class="panel admin-section admin-tab-panel" role="tabpanel" id="admin-panel-maps" aria-labelledby="admin-tab-maps" data-tab-panel="maps" hidden>
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
            </section>

            <section class="panel admin-section admin-tab-panel" role="tabpanel" id="admin-panel-weather" aria-labelledby="admin-tab-weather" data-tab-panel="weather" hidden>
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
                                    <td>
                                        <span
                                            class="admin-location-status"
                                            data-lat="{{ $location->lat }}"
                                            data-lon="{{ $location->lon }}"
                                            aria-hidden="true"
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M5 13l4 4L19 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        {{ $location->label ?? $location->name }}
                                    </td>
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
            </section>

            <section class="panel admin-section admin-tab-panel" role="tabpanel" id="admin-panel-orders" aria-labelledby="admin-tab-orders" data-tab-panel="orders" hidden>
                <div class="admin-section-head">
                    <h3>Denda · Eskariak</h3>
                </div>
                <div class="table-scroll">
                    <table class="kilter-table admin-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Bezeroa</th>
                                <th>Egoera</th>
                                <th>Tot.</th>
                                <th>Ekintzak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr class="{{ $order->status === \App\Models\ShopOrder::STATUS_PENDING_PAYMENT ? 'admin-order-row is-pending' : 'admin-order-row' }}">
                                    <td class="admin-col-date">{{ $order->created_at?->format('Y-m-d') ?? '-' }}</td>
                                    <td>
                                        @if($order->user)
                                            <a class="user-profile-link" href="{{ route('users.public', $order->user) }}">{{ $order->user->username ?? $order->user->name }}</a>
                                        @else
                                            {{ $order->email }}
                                        @endif
                                    </td>
                                    <td>{{ $order->status_label }}</td>
                                    <td class="admin-col-total">{{ $order->total }} €</td>
                                    <td class="admin-actions">
                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-icon admin-view-order"
                                            data-id="{{ $order->id }}"
                                            data-user="{{ $order->user?->name ?? $order->user?->username ?? '' }}"
                                            data-user-url="{{ $order->user ? route('users.public', $order->user) : '' }}"
                                            data-username="{{ $order->user?->username ?? '' }}"
                                            data-email="{{ $order->email }}"
                                            data-total="{{ $order->total }}"
                                            data-status="{{ $order->status }}"
                                            data-status-label="{{ $order->status_label }}"
                                            data-notes="{{ $order->notes ?? '' }}"
                                            data-created="{{ $order->created_at?->format('Y-m-d H:i') ?? '-' }}"
                                            data-items='@json($order->items_payload)'
                                            data-confirm="{{ route('admin.orders.confirm', $order) }}"
                                            data-delete="{{ route('admin.orders.delete', $order) }}"
                                            aria-label="Ikusi"
                                            title="Ikusi"
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s4-6 10-6 10 6 10 6-4 6-10 6-10-6-10-6z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Ez dago eskaririk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
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
            <div class="admin-avatar-preview">
                <img id="admin-edit-avatar" alt="Avatar" src="/images/default-avatar.svg">
            </div>
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

<div class="modal-shell hidden-modal" id="admin-order-modal" role="dialog" aria-modal="true" aria-labelledby="admin-order-title">
    <div class="modal-card">
        <div class="modal-head">
            <h2 id="admin-order-title">Eskaria</h2>
            <button type="button" class="icon-btn" id="close-order-modal">×</button>
        </div>
        <div class="admin-order-detail" id="admin-order-detail"></div>
        <div class="admin-confirm-actions">
            <button type="button" class="btn btn-secondary" id="admin-order-cancel">Utzi</button>
            <form method="POST" action="#" id="admin-order-confirm-form" hidden>
                @csrf
                <button type="submit" class="btn btn-primary" id="admin-order-confirm">Ordainketa baieztatu</button>
            </form>
            <form method="POST" action="#" id="admin-order-delete-form">
                @csrf
                @method('DELETE')
            <button type="button" class="btn btn-danger" id="admin-order-delete">Ezabatu</button>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const adminTabsRoot = document.querySelector('[data-admin-tabs]');
        const adminTabs = Array.from(document.querySelectorAll('.admin-tab'));
        const adminPanels = Array.from(document.querySelectorAll('.admin-tab-panel'));
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
        const orderModal = document.getElementById('admin-order-modal');
        const orderDetail = document.getElementById('admin-order-detail');
        const orderClose = document.getElementById('close-order-modal');
        const orderCancel = document.getElementById('admin-order-cancel');
        const orderConfirmForm = document.getElementById('admin-order-confirm-form');
        const orderConfirmBtn = document.getElementById('admin-order-confirm');
        const orderDeleteForm = document.getElementById('admin-order-delete-form');
        const orderDeleteBtn = document.getElementById('admin-order-delete');

        const editForm = document.getElementById('admin-user-edit-form');
        const editName = document.getElementById('admin-edit-name');
        const editUsername = document.getElementById('admin-edit-username');
        const editEmail = document.getElementById('admin-edit-email');
        const editAdmin = document.getElementById('admin-edit-admin');
        const editAvatar = document.getElementById('admin-edit-avatar');

        function openModal(modal) {
            modal?.classList.remove('hidden-modal');
        }

        function closeModal(modal) {
            modal?.classList.add('hidden-modal');
        }

        function activateTab(tabKey) {
            if (!tabKey) {
                return;
            }

            adminTabs.forEach((tab) => {
                const isActive = tab.dataset.tabTarget === tabKey;
                tab.classList.toggle('is-active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                tab.tabIndex = isActive ? 0 : -1;
            });

            adminPanels.forEach((panel) => {
                const isActive = panel.dataset.tabPanel === tabKey;
                panel.classList.toggle('is-active', isActive);
                panel.hidden = !isActive;
            });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        if (adminTabsRoot) {
            adminTabs.forEach((tab, index) => {
                tab.addEventListener('click', () => activateTab(tab.dataset.tabTarget));
                tab.addEventListener('keydown', (event) => {
                    if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
                        return;
                    }

                    event.preventDefault();
                    const direction = event.key === 'ArrowRight' ? 1 : -1;
                    const nextIndex = (index + direction + adminTabs.length) % adminTabs.length;
                    const nextTab = adminTabs[nextIndex];
                    activateTab(nextTab.dataset.tabTarget);
                    nextTab.focus();
                });
            });

            activateTab(adminTabs.find((tab) => tab.classList.contains('is-active'))?.dataset.tabTarget || adminTabs[0]?.dataset.tabTarget);
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
                if (editAvatar) {
                    editAvatar.src = button.dataset.avatar || '/images/default-avatar.svg';
                }
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

        document.querySelectorAll('.admin-view-order').forEach((button) => {
            button.addEventListener('click', () => {
                const items = JSON.parse(button.dataset.items || '[]');
                const itemsMarkup = items.map((item) => `
                    <li>${escapeHtml(item.name)} · ${escapeHtml(item.color)} · ${escapeHtml(item.size)} · x${escapeHtml(item.qty)} <strong>${escapeHtml(item.line_total)} €</strong></li>
                `).join('');
                const notes = button.dataset.notes ? `
                    <div class="admin-order-section">
                        <div class="admin-order-label">Oharrak</div>
                        <div class="admin-order-value">${escapeHtml(button.dataset.notes)}</div>
                    </div>
                ` : '';
                const customerName = escapeHtml(button.dataset.user || button.dataset.username || '-');
                const customerUrl = button.dataset.userUrl || '';
                const customerMarkup = customerUrl
                    ? `<a class="admin-order-user-link" href="${escapeHtml(customerUrl)}">${customerName}</a>`
                    : customerName;
                orderDetail.innerHTML = `
                    <div class="admin-order-detail-grid">
                        <div>
                            <div class="admin-order-label">Data</div>
                            <div class="admin-order-value">${escapeHtml(button.dataset.created)}</div>
                        </div>
                        <div>
                            <div class="admin-order-label">Bezeroa</div>
                            <div class="admin-order-value">${customerMarkup}</div>
                            <div class="admin-order-sub">${escapeHtml(button.dataset.email)}</div>
                        </div>
                        <div>
                            <div class="admin-order-label">Egoera</div>
                            <div class="admin-order-value">${escapeHtml(button.dataset.statusLabel || '-')}</div>
                        </div>
                        <div>
                            <div class="admin-order-label">Guztira</div>
                            <div class="admin-order-value">${escapeHtml(button.dataset.total)} €</div>
                        </div>
                    </div>
                    <div class="admin-order-section">
                        <div class="admin-order-label">Artikuluak</div>
                        <ul class="admin-order-items">${itemsMarkup}</ul>
                    </div>
                    ${notes}
                `;
                orderConfirmForm.action = button.dataset.confirm;
                orderDeleteForm.action = button.dataset.delete;
                if (button.dataset.status === 'pending_payment') {
                    orderConfirmForm.hidden = false;
                } else {
                    orderConfirmForm.hidden = true;
                }
                openModal(orderModal);
            });
        });

        orderClose?.addEventListener('click', () => closeModal(orderModal));
        orderCancel?.addEventListener('click', () => closeModal(orderModal));
        orderDeleteBtn?.addEventListener('click', () => {
            confirmText.textContent = 'Eskaria ezabatu nahi duzu?';
            confirmForm.action = orderDeleteForm.action;
            closeModal(orderModal);
            openModal(confirmModal);
        });

        const statusMarkers = Array.from(document.querySelectorAll('.admin-location-status'));
        if (statusMarkers.length) {
            const fetchWithTimeout = async (url, timeoutMs = 7000) => {
                if (window.appFetch) {
                    return window.appFetch(url, { method: 'GET', timeoutMs, showError: false });
                }
                const controller = new AbortController();
                const timeoutId = window.setTimeout(() => controller.abort(), timeoutMs);
                try {
                    return await fetch(url, { method: 'GET', signal: controller.signal });
                } finally {
                    window.clearTimeout(timeoutId);
                }
            };

            let hadError = false;
            const checks = statusMarkers.map(async (marker) => {
                const lat = marker.dataset.lat;
                const lon = marker.dataset.lon;
                if (!lat || !lon) return;
                const apiUrl = new URL('https://api.open-meteo.com/v1/forecast');
                apiUrl.searchParams.set('latitude', String(lat));
                apiUrl.searchParams.set('longitude', String(lon));
                apiUrl.searchParams.set('daily', 'weathercode');
                apiUrl.searchParams.set('forecast_days', '1');
                apiUrl.searchParams.set('timezone', 'Europe/Madrid');

                try {
                    const response = await fetchWithTimeout(apiUrl.toString());
                    if (response.ok) {
                        marker.classList.add('is-ok');
                        marker.setAttribute('title', 'Eguraldi API ondo');
                    }
                } catch (error) {
                    hadError = true;
                }
            });

            Promise.allSettled(checks).then(() => {
                if (hadError) {
                    window.showSnackbar?.('Ezin izan da eguraldi APIa egiaztatu.');
                }
            });
        }

        [createModal, editModal, confirmModal, locationCreateModal, orderModal].forEach((modal) => {
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) closeModal(modal);
            });
        });
    })();
</script>
@endsection
