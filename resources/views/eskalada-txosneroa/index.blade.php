@extends('layouts.app', ['title' => 'Eskalada Txosneroa | Sillarri Climb'])

@section('content')
<section class="txosna-page">
    <div class="panel txosna-hero">
        <div class="txosna-hero-copy">
            <p class="eyebrow">{{ $event['eyebrow'] }}</p>
            <h1>{{ $event['title'] }}</h1>
            <p class="lead">{{ $event['intro'] }}</p>
            <p class="txosna-note">{{ $event['download_note'] }}</p>
        </div>
    </div>

    @forelse($years as $year)
        <section class="panel txosna-year">
            <div class="txosna-year-head">
                <div>
                    <p class="eyebrow">Urtez urte</p>
                    <h2>{{ $year['headline'] }}</h2>
                    @if(! empty($year['summary']))
                        <p class="txosna-year-summary">{{ $year['summary'] }}</p>
                    @endif
                </div>

                <div class="txosna-year-meta">
                    <span>{{ count($year['images']) }} argazki</span>
                    <span>{{ count($year['videos']) }} bideo</span>
                </div>
            </div>

            <div class="txosna-winners">
                <h3>Irabazleak</h3>
                @if(empty($year['winners']))
                    <p class="txosna-empty-copy">Oraindik ez da urte honetako irabazleen zerrenda gehitu.</p>
                @else
                    <div class="txosna-winner-grid">
                        @foreach($year['winners'] as $winner)
                            <article class="txosna-winner-card">
                                <span>{{ $winner['label'] }}</span>
                                <strong>{{ $winner['name'] }}</strong>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

            @if(! empty($year['videos']))
                <div class="txosna-media-block">
                    <div class="txosna-section-head">
                        <h3>Bideoak</h3>
                    </div>
                    <div class="txosna-media-grid txosna-video-grid">
                        @foreach($year['videos'] as $video)
                            <article class="txosna-media-card">
                                <video controls preload="metadata" class="txosna-video-player">
                                    <source src="{{ $video['url'] }}">
                                    Zure nabigatzaileak ez du bideo hau erakusten.
                                </video>
                                <div class="txosna-media-copy">
                                    <h4>{{ $video['title'] }}</h4>
                                    <p>{{ $video['extension'] }} · {{ $video['size'] }}</p>
                                </div>
                                <a class="btn btn-secondary txosna-download" href="{{ $video['url'] }}" download="{{ $video['download_name'] }}">
                                    Deskargatu
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(! empty($year['images']))
                <div class="txosna-media-block">
                    <div class="txosna-section-head">
                        <h3>Argazkiak</h3>
                    </div>
                    <div class="txosna-media-grid">
                        @foreach($year['images'] as $image)
                            <article class="txosna-media-card">
                                <a class="txosna-media-link" href="{{ $image['url'] }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ $image['url'] }}" alt="{{ $image['title'] }}" class="txosna-image">
                                </a>
                                <div class="txosna-media-copy">
                                    <h4>{{ $image['title'] }}</h4>
                                    <p>{{ $image['extension'] }} · {{ $image['size'] }}</p>
                                </div>
                                <a class="btn btn-secondary txosna-download" href="{{ $image['url'] }}" download="{{ $image['download_name'] }}">
                                    Deskargatu
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(empty($year['images']) && empty($year['videos']))
                <p class="txosna-empty-copy">
                    Oraindik ez dago urte honetako multimedia edukirik. Gehitu fitxategiak
                    <code>public/media/eskalada-txosneroa/{{ $year['year'] }}/</code> karpetan eta automatikoki agertuko dira hemen.
                </p>
            @endif
        </section>
    @empty
        <section class="panel txosna-year">
            <h2>Urtez urteko bilduma prestatzen</h2>
            <p class="txosna-empty-copy">Oraindik ez dago ediziorik konfiguratuta edo multimedia materialik erabilgarri.</p>
        </section>
    @endforelse
</section>
@endsection
