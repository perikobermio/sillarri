<div
    class="panel user-public-completions"
    id="{{ $panelId }}"
    data-public-block-panel
>
    <h3>{{ $title }}</h3>
    @if($blocks->isEmpty())
        <p>{{ $emptyText }}</p>
    @else
        <div class="table-scroll user-public-table-wrap">
            <table class="kilter-table user-public-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Izena</th>
                        <th>Gradua</th>
                        <th>Mapa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($blocks as $block)
                        <tr>
                            <td>{{ $block->id }}</td>
                            <td>
                                <a href="{{ route('kilter.show', $block) }}">{{ $block->name }}</a>
                            </td>
                            <td>{{ $block->grade }}</td>
                            <td>{{ $block->map?->name ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($blocks->hasPages())
            <div class="kilter-pagination user-public-pagination" role="navigation" aria-label="{{ $title }}">
                <div class="pagination-summary">
                    Orria {{ $blocks->currentPage() }} / {{ $blocks->lastPage() }}
                </div>
                <div class="pagination-actions">
                    @if($blocks->onFirstPage())
                        <span class="pagination-btn is-disabled">Aurrekoa</span>
                    @else
                        <a class="pagination-btn user-public-pagination-link" href="{{ $blocks->previousPageUrl() }}">Aurrekoa</a>
                    @endif

                    @if($blocks->hasMorePages())
                        <a class="pagination-btn user-public-pagination-link" href="{{ $blocks->nextPageUrl() }}">Hurrengoa</a>
                    @else
                        <span class="pagination-btn is-disabled">Hurrengoa</span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>
