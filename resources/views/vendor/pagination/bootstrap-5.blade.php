@if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 w-100" aria-label="Pagination Navigation">
        <div class="text-muted small order-2 order-sm-1 text-center text-sm-start">
            মোট <span class="fw-semibold text-dark">{{ $paginator->total() }}</span> টির মধ্যে 
            <span class="fw-semibold text-dark">{{ $paginator->firstItem() }}</span> - 
            <span class="fw-semibold text-dark">{{ $paginator->lastItem() }}</span> দেখানো হচ্ছে
        </div>

        <div class="order-1 order-sm-2">
            <ul class="pagination pagination-sm mb-0 shadow-none">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                        <span class="page-link rounded-2 me-1" aria-hidden="true">&laquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link rounded-2 me-1" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">&laquo;</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link rounded-2 me-1">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link rounded-2 me-1">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link rounded-2 me-1" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link rounded-2" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">&raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="Next">
                        <span class="page-link rounded-2" aria-hidden="true">&raquo;</span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
