@if ($paginator->hasPages())
    <div class="btn-group btn-group-sm">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button class="btn btn-default" disabled="disabled">&laquo;</button>
        @else
            <button type="submit" name="page" value="{{ $paginator->currentPage() - 1 }}" class="btn btn-default">&laquo;</button>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <button class="btn btn-default" disabled="disabled">{{ $element }}</button>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <button class="btn btn-primary active" disabled="disabled">{{ $page }}</button>
                    @else
                        <button type="submit" name="page" value="{{ $page }}" class="btn btn-default">{{ $page }}</button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <button type="submit" name="page" value="{{ $paginator->currentPage() + 1 }}" class="btn btn-default">&raquo;</button>
        @else
            <button class="btn btn-default" disabled="disabled">&raquo;</button>
        @endif
    </div>
@endif
