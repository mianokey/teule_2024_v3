<!-- resources/views/custom_pagination.blade.php -->
<div class="pagination-area">
    <ul>
        @if ($paginator->onFirstPage())
            <li><a href="#" disabled>Prev</a></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}">Prev</a></li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li><a href="#" disabled>{{ $element }}</a></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><a class="active" href="#">{{ $page }}</a></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}">Next</a></li>
        @else
            <li><a href="#" disabled>Next</a></li>
        @endif
    </ul>
</div>
