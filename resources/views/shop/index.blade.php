@extends('layouts.app', ['title' => 'Denda | Sillarri Climb'])

@section('content')
@php
    $colors = [
        ['code' => 'BK', 'label' => 'Baltza', 'hex' => '#0d0d0d'],
        ['code' => 'WH', 'label' => 'Zurije', 'hex' => '#f3f2ee'],
        ['code' => 'RD', 'label' => 'Gorrije', 'hex' => '#c0392b'],
        ['code' => 'AZ', 'label' => 'Urdine', 'hex' => '#2a6ee8'],
        ['code' => 'RB', 'label' => 'Urdin ilune', 'hex' => '#1f3a66'],
        ['code' => 'BR', 'label' => 'Marroie', 'hex' => '#7a4b2e'],
        ['code' => 'SY', 'label' => 'Horije', 'hex' => '#f0c540'],
        ['code' => 'AS', 'label' => 'Grise', 'hex' => '#8e8b84'],
        ['code' => 'SB', 'label' => 'Steel blue', 'hex' => '#5a7d9a'],
        ['code' => 'SA', 'label' => 'Sand', 'hex' => '#d6c2a6'],
        ['code' => 'PV', 'label' => 'Pink vintage', 'hex' => '#c88a9a'],
        ['code' => 'LI', 'label' => 'Lima', 'hex' => '#a3d74f'],
        ['code' => 'AQ', 'label' => 'Aqua', 'hex' => '#3bb7b3'],
    ];

    $products = [
        [
            'name' => 'Biserak',
            'price' => 22,
            'note' => 'Eskalada eta eguneroko estiloa.',
            'id' => 'biserak',
            'images' => [
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=900&q=80',
            ],
            'sizes' => ['UNI'],
        ],
        [
            'name' => 'Kamiseta kalekue',
            'price' => 22,
            'note' => 'Kotoia, erabilera egunero.',
            'id' => 'kamiseta-kalekue',
            'images' => [
                'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80',
            ],
            'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        ],
        [
            'name' => 'Kamiseta teknikue',
            'price' => 22,
            'note' => 'Ehun teknikoa, entrenamendurako.',
            'id' => 'kamiseta-teknikue',
            'images' => [
                'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=900&q=80',
            ],
            'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        ],
        [
            'name' => 'Kamiseta tirantedune',
            'price' => 22,
            'note' => 'Udako saioetarako arina.',
            'id' => 'kamiseta-tirantedune',
            'images' => [
                'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80',
            ],
            'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        ],
        [
            'name' => 'Sudaderie',
            'price' => 32,
            'note' => 'Hotzerako geruza erosoa.',
            'id' => 'sudaderie',
            'images' => [
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=900&q=80',
            ],
            'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        ],
    ];

    $colorIcons = [
        'BK' => '⚫',
        'WH' => '⚪',
        'RD' => '🔴',
        'AZ' => '🔵',
        'RB' => '🔷',
        'BR' => '🟤',
        'SY' => '🟡',
        'AS' => '⚙️',
        'SB' => '🔹',
        'SA' => '🟫',
        'PV' => '🩷',
        'LI' => '🟢',
        'AQ' => '🩵',
    ];
@endphp

<section class="shop-hero">
    <div>
        <p class="eyebrow">Denda</p>
        <h1>Sillarri Merch</h1>
        <p class="lead">Gure komunitatearen sinadura: koloreak, ehunak eta eskalada giroa.</p>
    </div>
</section>

<section class="shop-grid">
    @foreach($products as $product)
        <article class="shop-card">
            <div class="shop-card-carousel" data-carousel>
                <div class="shop-card-carousel-track" data-track>
                    @foreach($product['images'] as $index => $image)
                        <img class="shop-card-image" src="{{ $image }}" alt="{{ $product['name'] }} {{ $index + 1 }}">
                    @endforeach
                </div>
                <button type="button" class="shop-carousel-btn prev" data-prev aria-label="Aurrekoa">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                </button>
                <button type="button" class="shop-carousel-btn next" data-next aria-label="Hurrengoa">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                </button>
            </div>
            <div class="shop-card-top">
                <span class="shop-price">{{ $product['price'] }} €</span>
                <h3>{{ $product['name'] }}</h3>
            </div>
            <p>{{ $product['note'] }}</p>
            <div class="shop-card-form">
                <label>
                    Kolorea
                    <select data-color>
                        @foreach($colors as $color)
                            <option value="{{ $color['code'] }}">{{ $colorIcons[$color['code']] ?? '●' }} {{ $color['label'] }} ({{ $color['code'] }})</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Talla
                    <select data-size>
                        @foreach($product['sizes'] as $size)
                            <option value="{{ $size }}">{{ $size === 'UNI' ? 'Talla bakarra' : $size }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Kopurua
                    <input data-qty type="number" min="1" max="10" value="1">
                </label>
                <button
                    type="button"
                    class="btn btn-primary shop-action-btn shop-add-btn"
                    data-add
                    data-id="{{ $product['id'] }}"
                    data-name="{{ $product['name'] }}"
                    data-price="{{ $product['price'] }}"
                    aria-label="Saskira gehitu"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6h15l-2 10H8L6 6zm-2-2h2l1 4H3l1-4zM10 20a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm7 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/></svg>
                </button>
            </div>
        </article>
    @endforeach
</section>

<section class="shop-form">
    <div class="shop-form-card">
        <h3>Saskia</h3>
        <div id="shopCart" class="shop-cart-list">
            <div class="shop-cart-empty">Oraindik ez dago produkturik.</div>
        </div>
        <div class="shop-cart-total" id="shopTotal">Guztira: 0 €</div>
        <div class="shop-success" id="shopSuccess"></div>
        <div class="shop-form-actions">
            <button type="button" class="btn btn-secondary shop-action-btn" id="shopClear" aria-label="Saskia hustu">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 7h14l-1 14H6L5 7zm3-3h8l1 2H7l1-2z"/></svg>
                <span class="btn-text">Saskia hustu</span>
            </button>
            <button type="button" class="btn btn-primary shop-action-btn" id="shopCheckout" aria-label="Amaitu erosketa">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6h15l-2 10H8L6 6zm-2-2h2l1 4H3l1-4zM10 20a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm7 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/></svg>
                <span class="btn-text">Amaitu erosketa</span>
            </button>
        </div>
    </div>
</section>

<div class="modal-shell hidden-modal" id="shop-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="shop-confirm-title">
    <div class="modal-card shop-confirm-card">
        <div class="modal-head">
            <h2 id="shop-confirm-title">Erosketa berretsi</h2>
            <button type="button" class="icon-btn" id="shop-confirm-close">×</button>
        </div>
        <div id="shop-confirm-body"></div>
        <p class="shop-confirm-note">Zure eskaria BELAIDXE dendara bideratuko da. Produktuak prest daudenean email bidez abizatuko zaitugu helbide honetara: {{ auth()->user()->email }}.</p>
        <div class="shop-confirm-actions">
            <button type="button" class="btn btn-secondary" id="shop-confirm-cancel">Utzi</button>
            <button type="button" class="btn btn-primary" id="shop-confirm-ok">Ados</button>
        </div>
    </div>
</div>

<div class="shop-toast" id="shopToast" role="status" aria-live="polite"></div>

<script>
    (function () {
        const products = @json($products);
        const clearBtn = document.getElementById('shopClear');
        const checkoutBtn = document.getElementById('shopCheckout');
        const cartEl = document.getElementById('shopCart');
        const totalEl = document.getElementById('shopTotal');
        const toastEl = document.getElementById('shopToast');
        const confirmModal = document.getElementById('shop-confirm-modal');
        const confirmBody = document.getElementById('shop-confirm-body');
        const confirmClose = document.getElementById('shop-confirm-close');
        const confirmCancel = document.getElementById('shop-confirm-cancel');
        const confirmOk = document.getElementById('shop-confirm-ok');
        const successEl = document.getElementById('shopSuccess');

        const cart = [];

        function formatPrice(value) {
            return `${value.toFixed(2).replace('.00', '')} €`;
        }

        function renderCart() {
            if (!cart.length) {
                cartEl.innerHTML = '<div class="shop-cart-empty">Oraindik ez dago produkturik.</div>';
                totalEl.textContent = 'Guztira: 0 €';
                return;
            }

            let total = 0;
            cartEl.innerHTML = cart.map((item, index) => {
                total += item.price * item.qty;
                return `
                    <div class="shop-cart-item">
                        <div class="shop-cart-row">
                            <strong>${item.name}</strong>
                            <span>${formatPrice(item.price * item.qty)}</span>
                        </div>
                        <div class="shop-cart-meta">Kolorea: ${item.color} · Talla: ${item.size} · Kopurua: ${item.qty}</div>
                        <button type="button" class="btn btn-secondary shop-action-btn" data-remove="${index}" aria-label="Kendu">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/></svg>
                            <span class="btn-text">Kendu</span>
                        </button>
                    </div>
                `;
            }).join('');

            totalEl.textContent = `Guztira: ${formatPrice(total)}`;

            cartEl.querySelectorAll('[data-remove]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const idx = Number(btn.dataset.remove);
                    cart.splice(idx, 1);
                    renderCart();
                });
            });
        }

        function showToast(message) {
            if (!toastEl) return;
            toastEl.textContent = message;
            toastEl.classList.add('is-visible');
            window.clearTimeout(showToast._timer);
            showToast._timer = window.setTimeout(() => {
                toastEl.classList.remove('is-visible');
            }, 2200);
        }

        function openModal() {
            confirmModal?.classList.remove('hidden-modal');
        }

        function closeModal() {
            confirmModal?.classList.add('hidden-modal');
        }

        function renderConfirm() {
            if (!confirmBody) return;
            if (!cart.length) {
                confirmBody.innerHTML = '<p>Ez dago produkturik saskian.</p>';
                return;
            }
            let total = 0;
            const lines = cart.map((item) => {
                const lineTotal = item.price * item.qty;
                total += lineTotal;
                return `<li>${item.name} · ${item.color} · ${item.size} · x${item.qty} <strong>${formatPrice(lineTotal)}</strong></li>`;
            }).join('');
            confirmBody.innerHTML = `
                <ul class="shop-confirm-list">${lines}</ul>
                <div class="shop-confirm-total">Guztira: ${formatPrice(total)}</div>
            `;
        }

        document.querySelectorAll('[data-add]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const card = btn.closest('.shop-card');
                if (!card) return;
                const color = card.querySelector('[data-color]')?.value || 'BK';
                const size = card.querySelector('[data-size]')?.value || 'M';
                const qtyValue = card.querySelector('[data-qty]')?.value || 1;
                const qty = Math.max(1, Number(qtyValue));
                cart.push({
                    id: btn.dataset.id,
                    name: btn.dataset.name,
                    price: Number(btn.dataset.price || 0),
                    qty,
                    color,
                    size,
                });
                renderCart();
                showToast('Produktua saskira gehituta');
            });
        });

        clearBtn?.addEventListener('click', () => {
            cart.length = 0;
            renderCart();
        });

        checkoutBtn?.addEventListener('click', () => {
            renderConfirm();
            openModal();
        });

        confirmClose?.addEventListener('click', closeModal);
        confirmCancel?.addEventListener('click', closeModal);
        confirmOk?.addEventListener('click', async () => {
            if (!cart.length) {
                closeModal();
                return;
            }

            confirmOk.disabled = true;
            confirmOk.textContent = 'Bidaltzen...';

            try {
                const response = await fetch('{{ route('shop.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ items: cart }),
                });

                if (!response.ok) {
                    throw new Error('checkout_failed');
                }

                const result = await response.json();
                cart.length = 0;
                renderCart();
                closeModal();
                showToast('Erosketa baieztatuta');
                if (successEl) {
                    successEl.textContent = 'Erosketa baieztatuta. Email bidez jasoko duzu baieztapena.';
                    successEl.classList.add('is-visible');
                }
            } catch (error) {
                showToast('Ezin izan da erosketa bidali');
            } finally {
                confirmOk.disabled = false;
                confirmOk.textContent = 'Ados';
            }
        });

        confirmModal?.addEventListener('click', (event) => {
            if (event.target === confirmModal) closeModal();
        });

        renderCart();

        document.querySelectorAll('[data-carousel]').forEach((carousel) => {
            const track = carousel.querySelector('[data-track]');
            const slides = Array.from(track?.children || []);
            if (!track || slides.length === 0) return;
            let index = 0;

            function update() {
                track.style.transform = `translateX(-${index * 100}%)`;
            }

            carousel.querySelector('[data-prev]')?.addEventListener('click', () => {
                index = (index - 1 + slides.length) % slides.length;
                update();
            });

            carousel.querySelector('[data-next]')?.addEventListener('click', () => {
                index = (index + 1) % slides.length;
                update();
            });

            update();
        });
    })();
</script>
@endsection
