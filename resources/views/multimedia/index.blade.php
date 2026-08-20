@extends('layouts.app', ['title' => 'Multimedia | Sillarri Climb'])

@section('content')
<section class="multimedia-page">
    <div class="panel">
        <p class="eyebrow">Komunitatea</p>
        <h1>Multimedia</h1>
        <p>Igo zure argazkiak eta partekatu komunitatearekin.</p>
    </div>

    <div class="panel multimedia-upload">
        <h3>Argazkia igo</h3>
        <form method="POST" action="{{ route('multimedia.store') }}" enctype="multipart/form-data" class="auth-form">
            @csrf

            <label>Izena</label>
            <input type="text" name="title" value="{{ old('title') }}" required>
            @error('title')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Deskribapena</label>
            <textarea name="description" rows="3">{{ old('description') }}</textarea>
            @error('description')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Etiketak (koma bidez)</label>
            <input type="text" name="tags" value="{{ old('tags') }}">
            @error('tags')
                <small class="error">{{ $message }}</small>
            @enderror

            <label>Argazkia (max 20MB)</label>
            <input type="file" name="image" accept="image/*" required>
            @error('image')
                <small class="error">{{ $message }}</small>
            @enderror

            <button type="submit" class="btn btn-primary">Igo</button>
        </form>
    </div>

    <div class="panel multimedia-gallery">
        <h3>Argazkiak</h3>
        @if($photos->isEmpty())
            <p>Oraindik ez dago argazkirik.</p>
        @else
            <div class="multimedia-grid">
                @foreach($photos as $photo)
                    @php
                        $imagePath = $photo->image_path;
                        $imageUrl = \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://', '/'])
                            ? $imagePath
                            : '/storage/'.$imagePath;
                    @endphp
                    <button
                        type="button"
                        class="multimedia-thumb"
                        data-image="{{ $imageUrl }}"
                        data-title="{{ $photo->title }}"
                        data-description="{{ $photo->description }}"
                    >
                        <img src="{{ $imageUrl }}" alt="{{ $photo->title }}">
                        <span class="thumb-title">{{ $photo->title }}</span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</section>

<div class="modal-shell hidden-modal image-lightbox-modal" id="media-lightbox" role="dialog" aria-modal="true" aria-labelledby="media-lightbox-title">
    <div class="modal-card image-lightbox-card">
        <button type="button" class="image-lightbox-close" id="media-lightbox-close" aria-label="Itxi">×</button>
        <button type="button" class="image-lightbox-nav prev" id="media-lightbox-prev" aria-label="Aurrekoa">‹</button>
        <button type="button" class="image-lightbox-nav next" id="media-lightbox-next" aria-label="Hurrengoa">›</button>
        <div class="image-lightbox-stage">
            <img id="media-lightbox-image" class="image-lightbox-image" alt="Argazkia">
        </div>
        <div class="image-lightbox-caption">
            <h2 id="media-lightbox-title">Argazkia</h2>
            <p id="media-lightbox-description"></p>
        </div>
    </div>
</div>

<script>
    (function () {
        const buttons = document.querySelectorAll('.multimedia-thumb');
        const modal = document.getElementById('media-lightbox');
        const closeBtn = document.getElementById('media-lightbox-close');
        const prevBtn = document.getElementById('media-lightbox-prev');
        const nextBtn = document.getElementById('media-lightbox-next');
        const image = document.getElementById('media-lightbox-image');
        const description = document.getElementById('media-lightbox-description');
        const title = document.getElementById('media-lightbox-title');

        if (!modal || !image || !description || !title || !buttons.length) return;

        const items = Array.from(buttons).map((button) => ({
            image: button.dataset.image || '',
            title: button.dataset.title || 'Argazkia',
            description: button.dataset.description || '',
        }));

        const lightbox = window.initImageLightbox?.({
            modal,
            image,
            title,
            description,
            prevBtn,
            nextBtn,
            closeBtn,
            items,
        });

        if (!lightbox) return;

        buttons.forEach((button, index) => {
            button.addEventListener('click', () => lightbox.open(index));
        });
    })();
</script>
@endsection
