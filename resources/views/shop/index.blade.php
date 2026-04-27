@extends('layouts.app', ['title' => 'Denda | Sillarri Climb'])

@section('content')
<div class="shop-page" id="shopPage">
@php
    $colors = [
        ['code' => 'BK', 'label' => 'Black', 'hex' => '#1b1b1b'],
        ['code' => 'WH', 'label' => 'White', 'hex' => '#f3f2ee'],
        ['code' => 'RD', 'label' => 'Red', 'hex' => '#cf2f32'],
        ['code' => 'AZ', 'label' => 'Royal Blue', 'hex' => '#2a6ee8'],
        ['code' => 'RB', 'label' => 'Navy', 'hex' => '#21345f'],
        ['code' => 'BR', 'label' => 'Brown', 'hex' => '#7a4b2e'],
        ['code' => 'SY', 'label' => 'Gold', 'hex' => '#f0c540'],
        ['code' => 'AS', 'label' => 'Ash', 'hex' => '#c8c7c3'],
        ['code' => 'SB', 'label' => 'Steel Blue', 'hex' => '#5a7d9a'],
        ['code' => 'SA', 'label' => 'Sand', 'hex' => '#d6c2a6'],
        ['code' => 'PV', 'label' => 'Pink Vintage', 'hex' => '#c88a9a'],
        ['code' => 'LI', 'label' => 'Lima', 'hex' => '#a3d74f'],
        ['code' => 'AQ', 'label' => 'Aqua', 'hex' => '#3bb7b3'],
        ['code' => 'PGR', 'label' => 'Pacific Grey', 'hex' => '#8d949c'],
        ['code' => 'NV', 'label' => 'Navy', 'hex' => '#223050'],
        ['code' => 'RBL', 'label' => 'Royal Blue', 'hex' => '#205fcb'],
        ['code' => 'SWP', 'label' => 'Swimming Pool', 'hex' => '#22bfd2'],
        ['code' => 'SKB', 'label' => 'Sky Blue', 'hex' => '#74b8ec'],
        ['code' => 'IND', 'label' => 'Indigo', 'hex' => '#42538c'],
        ['code' => 'OR', 'label' => 'Orange', 'hex' => '#f27921'],
        ['code' => 'SBT', 'label' => 'Sorbet', 'hex' => '#f86d8f'],
        ['code' => 'PSX', 'label' => 'Pink Sixties', 'hex' => '#d871aa'],
        ['code' => 'KGR', 'label' => 'Kelly Green', 'hex' => '#00a64f'],
        ['code' => 'GLD', 'label' => 'Gold', 'hex' => '#edc512'],
        ['code' => 'SGR', 'label' => 'Sport Grey', 'hex' => '#9b9b9b'],
        ['code' => 'NAT', 'label' => 'Natural', 'hex' => '#efe5cf'],
        ['code' => 'UBK', 'label' => 'Used Black', 'hex' => '#3a3a3a'],
        ['code' => 'DGY', 'label' => 'Dark Grey', 'hex' => '#5a5d63'],
        ['code' => 'NVB', 'label' => 'Navy Blue', 'hex' => '#22345c'],
        ['code' => 'CBL', 'label' => 'Cobalt Blue', 'hex' => '#2554c7'],
        ['code' => 'MLI', 'label' => 'Millenial Lilac', 'hex' => '#a98bb9'],
        ['code' => 'ATL', 'label' => 'Atoll', 'hex' => '#13b3bd'],
        ['code' => 'DBL', 'label' => 'Diva Blue', 'hex' => '#4b87d9'],
        ['code' => 'STB', 'label' => 'Stone Blue', 'hex' => '#6f92a8'],
        ['code' => 'RPU', 'label' => 'Radiant Purple', 'hex' => '#8d52c8'],
        ['code' => 'UPU', 'label' => 'Urban Purple', 'hex' => '#6f4f9d'],
        ['code' => 'FRD', 'label' => 'Fire Red', 'hex' => '#d62828'],
        ['code' => 'UOR', 'label' => 'Urban Orange', 'hex' => '#ef6d2f'],
        ['code' => 'SOR', 'label' => 'Sunset Orange', 'hex' => '#f67c48'],
        ['code' => 'OPK', 'label' => 'Orchid Pink', 'hex' => '#d77fa1'],
        ['code' => 'BRG', 'label' => 'Burgundy', 'hex' => '#722f46'],
        ['code' => 'OGR', 'label' => 'Orchid Green', 'hex' => '#7ebd75'],
        ['code' => 'PXL', 'label' => 'Pixel Lime', 'hex' => '#c8d81c'],
        ['code' => 'MMT', 'label' => 'Millenial Mint', 'hex' => '#a6d8c4'],
        ['code' => 'BGR', 'label' => 'Bottle Green', 'hex' => '#0f5a3e'],
        ['code' => 'SYL', 'label' => 'Solar Yellow', 'hex' => '#f2df12'],
        ['code' => 'CHO', 'label' => 'Chocolate', 'hex' => '#5c3825'],
        ['code' => 'ASH', 'label' => 'Ash', 'hex' => '#cfcfcb'],
        ['code' => 'UKH', 'label' => 'Urban Khaki', 'hex' => '#7b7257'],
        ['code' => 'SND', 'label' => 'Sand', 'hex' => '#b09f7c'],
        ['code' => 'APR', 'label' => 'Apricot', 'hex' => '#f6c18b'],
        ['code' => 'AQU', 'label' => 'Aqua', 'hex' => '#1db8c5'],
        ['code' => 'FUC', 'label' => 'Fuchsia', 'hex' => '#ea1b8d'],
        ['code' => 'YLW', 'label' => 'Yellow', 'hex' => '#f4d81d'],
        ['code' => 'FYL', 'label' => 'Fluor Yellow', 'hex' => '#d8e71b'],
        ['code' => 'CRL', 'label' => 'Coral', 'hex' => '#f56f73'],
        ['code' => 'LIM', 'label' => 'Lime', 'hex' => '#7ed23d'],
        ['code' => 'PUR', 'label' => 'Purple', 'hex' => '#3d3c93'],
        ['code' => 'ORN', 'label' => 'Orange', 'hex' => '#ff7a1c'],
        ['code' => 'OLV', 'label' => 'Olive', 'hex' => '#6c664e'],
    ];

    $products = [
        [
            'name' => 'Biserak',
            'price' => 15,
            'note' => 'Eskalada eta eguneroko estiloa.',
            'id' => 'biserak',
            'images' => [
                '/images/shop/bisera_1.png',
                '/images/shop/bisera_2.png',
            ],
            'variants' => [
                [
                    'id' => 'default',
                    'label' => 'Unica',
                    'sizes' => ['UNI'],
                    'colors' => ['BK', 'WH', 'RD', 'AZ', 'RB', 'BR', 'SY', 'AS', 'SB', 'SA', 'PV', 'LI', 'AQ'],
                ],
            ],
        ],
        [
            'name' => 'Kamiseta kalekue',
            'price' => 20,
            'note' => 'Kotoia, erabilera egunero.',
            'id' => 'kamiseta-kalekue',
            'images' => [
                '/images/shop/kalekue_back_1.png',
                '/images/shop/kalekue_front_1.png',
                '/images/shop/kalekue_back_2.png',
                '/images/shop/kalekue_front_2.png',
            ],
            'variants' => [
                [
                    'id' => 'default',
                    'label' => 'Unica',
                    'sizes' => ['3-4', '5-6', '7-8', '9-11', '12-14', 'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
                    'colors' => ['WH', 'BK', 'PGR', 'NV', 'RBL', 'SWP', 'SKB', 'IND', 'RD', 'OR', 'SBT', 'PSX', 'KGR', 'GLD', 'ASH', 'SGR', 'NAT', 'UBK', 'DGY', 'NVB', 'CBL', 'MLI', 'ATL', 'DBL', 'STB', 'RPU', 'UPU', 'FRD', 'UOR', 'SOR', 'OPK', 'BRG', 'OGR', 'PXL', 'MMT', 'BGR', 'SYL', 'BRN', 'CHO', 'UKH', 'SND', 'APR'],
                ],
            ],
        ],
        [
            'name' => 'Kamiseta teknikue',
            'price' => 20,
            'note' => 'Ehun teknikoa, entrenamendurako.',
            'id' => 'kamiseta-teknikue',
            'images' => [
                '/images/shop/teknika_front_1.png',
                '/images/shop/teknika_back_1.png',
                '/images/shop/teknika_front_2.png',
                '/images/shop/teknika_back_2.png',
            ],
            'variants' => [
                [
                    'id' => 'adult',
                    'label' => 'Adulto',
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
                    'colors' => ['WH', 'BK', 'NV', 'RBL', 'RD', 'AQU', 'FUC', 'DGY', 'YLW', 'FYL', 'SND', 'CRL', 'LIM', 'PUR', 'ORN', 'KGR', 'OLV'],
                ],
            ],
        ],
        [
            'name' => 'Kamiseta tirantedune',
            'price' => 20,
            'note' => 'Udako saioetarako arina.',
            'id' => 'kamiseta-tirantedune',
            'images' => [
                '/images/shop/tirante_back_1.png',
                '/images/shop/tirante_front_1.png',
                '/images/shop/tirante_1.png',
                '/images/shop/tirante_2.png',
            ],
            'variants' => [
                [
                    'id' => 'adult',
                    'label' => 'Adulto',
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
                    'colors' => ['WH', 'BK', 'SGR', 'CBL', 'FRD'],
                ],
            ],
        ],
        [
            'name' => 'Sudaderie',
            'price' => 25,
            'note' => 'Hotzerako geruza erosoa.',
            'id' => 'sudaderie',
            'images' => [
                '/images/shop/suda_front_2.png',
                '/images/shop/suda_back_2.png',
                '/images/shop/suda_back_1.png',
                '/images/shop/suda_front_1.png',
            ],
            'variants' => [
                [
                    'id' => 'adult',
                    'label' => 'Adulto',
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
                    'colors' => ['WH', 'BK', 'SGR', 'NV', 'RBL', 'RD', 'NAT', 'UBK', 'DGY', 'PGR', 'NVB', 'CBL', 'MLI', 'SWP', 'ATL', 'SKB', 'DBL', 'STB', 'RPU', 'UPU', 'FRD', 'UOR', 'OR', 'SBT', 'SOR', 'OPK', 'BRG', 'OGR', 'PXL', 'MMT', 'KGR', 'BGR', 'SYL', 'GLD', 'BRN', 'CHO', 'ASH', 'UKH', 'SND', 'APR'],
                ],
            ],
        ],
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
        @php
            $defaultVariant = $product['variants'][0];
        @endphp
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
                @if(count($product['variants']) > 1)
                    <label>
                        Modeloa
                        <select data-variant>
                            @foreach($product['variants'] as $variant)
                                <option value="{{ $variant['id'] }}">{{ $variant['label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif
                <label>
                    Kolorea
                    <select data-color class="shop-color-select map-select-hidden">
                        @foreach($defaultVariant['colors'] as $colorCode)
                            @php
                                $color = collect($colors)->firstWhere('code', $colorCode);
                            @endphp
                            @if($color)
                                <option value="{{ $color['code'] }}">{{ $color['label'] }} ({{ $color['code'] }})</option>
                            @endif
                        @endforeach
                    </select>
                    <div class="shop-color-picker" data-color-picker>
                        <button type="button" class="shop-color-trigger" data-color-trigger aria-haspopup="listbox" aria-expanded="false">
                            <span class="shop-color-swatch" data-color-swatch aria-hidden="true"></span>
                            <span data-color-label>Kolorea hautatu</span>
                            <span class="shop-color-caret" aria-hidden="true">▾</span>
                        </button>
                        <div class="shop-color-list" data-color-list role="listbox" aria-label="Koloreak"></div>
                    </div>
                </label>
                <label>
                    Talla
                    <select data-size>
                        @foreach($defaultVariant['sizes'] as $size)
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
    <div class="shop-form-card shop-cart-fixed" id="shopCartCard">
        <h3>Saskia</h3>
        <div id="shopCart" class="shop-cart-list">
            <div class="shop-cart-empty">Oraindik ez dago produkturik.</div>
        </div>
        <div class="shop-cart-footer">
            <div class="shop-cart-total" id="shopTotal">Guztira: 0 €</div>
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
        <div class="shop-success" id="shopSuccess"></div>
    </div>
</section>

<div class="modal-shell hidden-modal" id="shop-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="shop-confirm-title">
    <div class="modal-card shop-confirm-card">
        <div class="modal-head">
            <h2 id="shop-confirm-title">Erosketa berretsi</h2>
            <button type="button" class="icon-btn" id="shop-confirm-close">×</button>
        </div>
        <div id="shop-confirm-body"></div>
        <p class="shop-confirm-note">Eskaria jaso ondoren email bidez bidaliko dizugu ordainketa egiteko kontua. Ordainketa egiaztatutakoan bidaliko dugu eskaria BELAIDXEra.</p>
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
        const cartCard = document.getElementById('shopCartCard');
        const shopPage = document.getElementById('shopPage');
        const colorMeta = @json(collect($colors)->keyBy('code'));

        const cart = [];

        function formatPrice(value) {
            return `${value.toFixed(2).replace('.00', '')} €`;
        }

        function formatSizeLabel(size) {
            return /^\d+-\d+$/.test(String(size)) ? `${size} urte` : size;
        }

        function renderCart() {
            if (!cart.length) {
                cartEl.innerHTML = '<div class="shop-cart-empty">Oraindik ez dago produkturik.</div>';
                totalEl.textContent = 'Guztira: 0 €';
                cartCard?.classList.remove('is-fixed');
                shopPage?.classList.remove('has-cart');
                return;
            }

            let total = 0;
            cartEl.innerHTML = cart.map((item, index) => {
                total += item.price * item.qty;
                return `
                    <div class="shop-cart-item compact">
                        <div class="shop-cart-row">
                            <strong>${item.name}</strong>
                            <span>${formatPrice(item.price * item.qty)}</span>
                        </div>
                        <div class="shop-cart-meta">
                            ${item.variantLabel ? `Modeloa: ${item.variantLabel} · ` : ''}Kolorea: ${item.colorLabel || item.color} · Talla: ${item.sizeLabel || item.size} · Kopurua: ${item.qty}
                            <button type="button" class="btn btn-secondary shop-action-btn inline-remove" data-remove="${index}" aria-label="Kendu">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/></svg>
                                <span class="btn-text">Kendu</span>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            totalEl.textContent = `Guztira: ${formatPrice(total)}`;
            cartCard?.classList.add('is-fixed');
            shopPage?.classList.add('has-cart');

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
                return `<li>${item.name}${item.variantLabel ? ` · ${item.variantLabel}` : ''} · ${item.colorLabel || item.color} · ${item.sizeLabel || item.size} · x${item.qty} <strong>${formatPrice(lineTotal)}</strong></li>`;
            }).join('');
            confirmBody.innerHTML = `
                <ul class="shop-confirm-list">${lines}</ul>
                <div class="shop-confirm-total">Guztira: ${formatPrice(total)}</div>
                <label class="shop-confirm-label" for="shop-confirm-notes">Oharrak (aukerakoa)</label>
                <textarea id="shop-confirm-notes" class="shop-confirm-notes" rows="3" placeholder="Adibidez: neurriak, koloreari buruzko oharrak..."></textarea>
            `;
        }

        function fillSelect(select, values, formatter, selectedValue = '') {
            if (!select) return;
            const normalizedSelected = String(selectedValue || '');
            select.innerHTML = values.map((value) => {
                const optionLabel = formatter(value);
                const selected = String(value) === normalizedSelected ? ' selected' : '';
                return `<option value="${value}"${selected}>${optionLabel}</option>`;
            }).join('');
        }

        function closeColorPicker(card) {
            const trigger = card?.querySelector('[data-color-trigger]');
            const list = card?.querySelector('[data-color-list]');
            if (!trigger || !list) return;
            list.classList.remove('is-open');
            trigger.setAttribute('aria-expanded', 'false');
        }

        function syncColorPicker(card) {
            const colorSelect = card?.querySelector('[data-color]');
            const swatch = card?.querySelector('[data-color-swatch]');
            const label = card?.querySelector('[data-color-label]');
            const list = card?.querySelector('[data-color-list]');
            if (!colorSelect || !swatch || !label || !list) return;

            const color = colorMeta[colorSelect.value];
            swatch.style.background = color?.hex || '#ffffff';
            label.textContent = color ? `${color.label} (${color.code})` : 'Kolorea hautatu';

            list.querySelectorAll('.shop-color-option').forEach((option) => {
                option.setAttribute('aria-selected', option.dataset.colorCode === colorSelect.value ? 'true' : 'false');
            });
        }

        function renderColorPicker(card, colorCodes, selectedValue = '') {
            const colorSelect = card?.querySelector('[data-color]');
            const list = card?.querySelector('[data-color-list]');
            if (!colorSelect || !list) return;

            fillSelect(
                colorSelect,
                colorCodes,
                (code) => {
                    const color = colorMeta[code];
                    return `${color?.label || code} (${code})`;
                },
                selectedValue
            );

            list.innerHTML = colorCodes.map((code) => {
                const color = colorMeta[code];
                if (!color) return '';
                const selected = code === colorSelect.value ? 'true' : 'false';
                return `
                    <button type="button" class="shop-color-option" data-color-code="${color.code}" role="option" aria-selected="${selected}">
                        <span class="shop-color-swatch" style="background:${color.hex}" aria-hidden="true"></span>
                        <span>${color.label} (${color.code})</span>
                    </button>
                `;
            }).join('');

            list.querySelectorAll('.shop-color-option').forEach((option) => {
                option.addEventListener('click', () => {
                    colorSelect.value = option.dataset.colorCode || '';
                    syncColorPicker(card);
                    closeColorPicker(card);
                });
            });

            syncColorPicker(card);
        }

        function getProductById(productId) {
            return products.find((product) => product.id === productId) || null;
        }

        function getVariant(product, variantId) {
            if (!product || !Array.isArray(product.variants) || product.variants.length === 0) return null;
            return product.variants.find((variant) => variant.id === variantId) || product.variants[0];
        }

        function renderCardOptions(card) {
            const productId = card?.querySelector('[data-add]')?.dataset.id || '';
            const product = getProductById(productId);
            if (!product) return;

            const variantSelect = card.querySelector('[data-variant]');
            const colorSelect = card.querySelector('[data-color]');
            const sizeSelect = card.querySelector('[data-size]');
            const activeVariant = getVariant(product, variantSelect?.value || product.variants[0].id);
            if (!activeVariant) return;

            fillSelect(
                sizeSelect,
                activeVariant.sizes,
                (size) => size === 'UNI' ? 'Talla bakarra' : formatSizeLabel(size),
                sizeSelect?.value
            );

            renderColorPicker(card, activeVariant.colors, colorSelect?.value);
        }

        document.querySelectorAll('[data-add]').forEach((btn) => {
            const card = btn.closest('.shop-card');
            if (card) {
                card.querySelector('[data-variant]')?.addEventListener('change', () => renderCardOptions(card));
                card.querySelector('[data-color-trigger]')?.addEventListener('click', () => {
                    const list = card.querySelector('[data-color-list]');
                    const trigger = card.querySelector('[data-color-trigger]');
                    if (!list || !trigger) return;
                    const isOpen = list.classList.contains('is-open');
                    document.querySelectorAll('.shop-color-list.is-open').forEach((openList) => {
                        openList.classList.remove('is-open');
                    });
                    document.querySelectorAll('[data-color-trigger][aria-expanded="true"]').forEach((openTrigger) => {
                        openTrigger.setAttribute('aria-expanded', 'false');
                    });
                    if (!isOpen) {
                        list.classList.add('is-open');
                        trigger.setAttribute('aria-expanded', 'true');
                    }
                });
                renderCardOptions(card);
            }

            btn.addEventListener('click', () => {
                const card = btn.closest('.shop-card');
                if (!card) return;
                const product = getProductById(btn.dataset.id || '');
                if (!product) return;
                const variantSelect = card.querySelector('[data-variant]');
                const variant = getVariant(product, variantSelect?.value || product.variants[0].id);
                if (!variant) return;
                const color = card.querySelector('[data-color]')?.value || variant.colors[0];
                const size = card.querySelector('[data-size]')?.value || 'M';
                const qtyValue = card.querySelector('[data-qty]')?.value || 1;
                const qty = Math.max(1, Number(qtyValue));
                const colorInfo = colorMeta[color] || null;
                cart.push({
                    id: btn.dataset.id,
                    name: btn.dataset.name,
                    price: Number(btn.dataset.price || 0),
                    qty,
                    variant: variant.id,
                    variantLabel: product.variants.length > 1 ? variant.label : '',
                    color,
                    colorLabel: colorInfo?.label || color,
                    size,
                    sizeLabel: formatSizeLabel(size),
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

            window.setButtonLoading?.(confirmOk, true);

            try {
                const notesValue = document.getElementById('shop-confirm-notes')?.value || '';
                const requestOptions = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ items: cart, notes: notesValue }),
                };
                const response = window.appFetch
                    ? await window.appFetch('{{ route('shop.checkout') }}', { ...requestOptions, timeoutMs: 10000, showError: false })
                    : await fetch('{{ route('shop.checkout') }}', requestOptions);

                const result = await response.json().catch(() => ({}));
                if (!response.ok) {
                    if (result?.code === 'missing_phone' && result?.redirect) {
                        closeModal();
                        showToast(result.message || 'Telefonoa beharrezkoa da eskaria egiteko.');
                        window.setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 700);
                        return;
                    }
                    throw new Error(result.message || 'checkout_failed');
                }
                cart.length = 0;
                renderCart();
                closeModal();
                showToast(result.message || 'Eskaria jasota');
                if (successEl) {
                    successEl.textContent = 'Eskaria jasota. Email bidez jasoko duzu ordainketa egiteko informazioa.';
                    successEl.classList.add('is-visible');
                }
            } catch (error) {
                const message = error?.name === 'AbortError'
                    ? 'Denbora agortu da. Saiatu berriro.'
                    : (error?.message || 'Ezin izan da erosketa bidali');
                showToast(message);
            } finally {
                window.setButtonLoading?.(confirmOk, false);
            }
        });

        confirmModal?.addEventListener('click', (event) => {
            if (event.target === confirmModal) closeModal();
        });

        document.addEventListener('click', (event) => {
            document.querySelectorAll('.shop-card').forEach((card) => {
                const picker = card.querySelector('[data-color-picker]');
                if (!picker || picker.contains(event.target)) return;
                closeColorPicker(card);
            });
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
</div>
@endsection
