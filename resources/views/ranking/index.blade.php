@extends('layouts.app', ['title' => 'Sailkapena | Sillarri Climb'])

@section('content')
<section class="kilter-page">
    <div class="kilter-table-wrap">
        <div class="kilter-table-head">
            <div>
                <p class="eyebrow">Sillarri League</p>
                <h1>HALL OF SHAME</h1>
            </div>
        </div>

        <div class="ranking-table-wrap">
            <table class="kilter-table ranking-table">
                <colgroup>
                    <col class="ranking-col-rank">
                    <col class="ranking-col-user">
                    <col class="ranking-col-score">
                </colgroup>
                <thead>
                    <tr>
                        <th class="ranking-col-rank" aria-label="Posizioa"></th>
                        <th>Erabiltzailea</th>
                        <th>Puntuazioa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ranking as $i => $row)
                        <tr>
                            <td><strong>{{ $i + 1 }}</strong></td>
                            <td>
                                <a class="ranking-user-link" href="{{ route('users.public', ['user' => $row['user_id']]) }}">
                                    {{ $row['username'] }}
                                </a>
                            </td>
                            <td><strong>{{ number_format($row['score'], 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Oraindik ez dago daturik sailkapenerako.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
