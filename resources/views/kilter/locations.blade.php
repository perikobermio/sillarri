@extends('layouts.app', ['title' => 'Kokapenak | KILTER'])

@section('content')
<section class="kilter-page">
    <div class="kilter-table-head">
        <div>
            <p class="eyebrow">Kilter Board Hub</p>
            <h1>KOKAPENAK</h1>
            <p class="locations-intro">
                Kokapena bakoitzean dauden mapak eta bertako blokeak. Txartel bat sakatzean, zerrenda kokapen horretara iragazita irekiko da.
            </p>
        </div>
    </div>

    @if(empty($locationCards))
        <div class="panel">
            <p>Oraindik ez dago kokapenik erregistratuta.</p>
        </div>
    @else
        <div class="location-card-grid">
            @foreach($locationCards as $locationCard)
                <a
                    class="location-card"
                    href="{{ route('kilter', ['location' => $locationCard['key']]) }}"
                    aria-label="{{ $locationCard['name'] }} kokapeneko blokeak ikusi"
                >
                    <div
                        class="location-card-media {{ count($locationCard['images']) > 1 ? 'has-rotation' : '' }}"
                        @if(count($locationCard['images']) > 0)
                            data-rotation-images='@json($locationCard["images"])'
                        @endif
                    >
                        @if(count($locationCard['images']) > 0)
                            <img
                                src="{{ $locationCard['images'][0] }}"
                                alt="{{ $locationCard['name'] }} kokapeneko mapa"
                                class="location-card-image is-active"
                                loading="lazy"
                            >
                        @else
                            <div class="location-card-placeholder">MAP</div>
                        @endif
                        <div class="location-card-overlay">
                            <span>{{ $locationCard['maps_count'] }} mapa</span>
                        </div>
                        @if(count($locationCard['images']) > 1)
                            <button type="button" class="location-card-nav prev" data-direction="-1" aria-label="Aurreko mapa">‹</button>
                            <button type="button" class="location-card-nav next" data-direction="1" aria-label="Hurrengo mapa">›</button>
                        @endif
                    </div>

                    <div class="location-card-body">
                        <h2>{{ $locationCard['name'] }}</h2>
                        <p>{{ $locationCard['blocks_count'] }} bloke</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>

<script>
    (function () {
        const cards = document.querySelectorAll('.location-card-media.has-rotation');

        cards.forEach((card) => {
            const images = JSON.parse(card.dataset.rotationImages || '[]');
            if (images.length < 2) return;

            const currentImage = card.querySelector('.location-card-image');
            if (!currentImage) return;

            let index = 0;
            const preload = new Set([images[0]]);

            const ensurePreloaded = (src) => {
                if (!src || preload.has(src)) return;
                const img = new Image();
                img.src = src;
                preload.add(src);
            };
            const goTo = (nextIndex) => {
                index = (nextIndex + images.length) % images.length;
                ensurePreloaded(images[(index + 1) % images.length]);
                currentImage.classList.remove('is-active');

                window.setTimeout(() => {
                    currentImage.src = images[index];
                    currentImage.classList.add('is-active');
                }, 180);
            };

            card.querySelectorAll('.location-card-nav').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    const direction = Number(button.dataset.direction || '1');
                    goTo(index + direction);
                });
            });
        });
    })();
</script>
@endsection
