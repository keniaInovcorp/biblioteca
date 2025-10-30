@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm opacity-60 px-1 mb-2 sm:mb-0">
            @php
                $from = ($paginator->firstItem() ?? 0);
                $to = ($paginator->lastItem() ?? 0);
                $total = $paginator->total();
            @endphp
            Mostrando {{ $from }} a {{ $to }} de {{ $total }} resultados
        </div>

        <nav role="navigation" aria-label="Pagination Navigation" class="">
            <span class="hidden">
                <!-- Hidden for a11y, PT labels below used on controls -->
            </span>
            <div class="join">
                <!-- Previous Page Link -->
                @if ($paginator->onFirstPage())
                    <span class="join-item btn btn-disabled" aria-disabled="true" aria-label="Anterior">‹</span>
                @else
                    <a class="join-item btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior">‹</a>
                @endif

                <!-- Pagination Elements -->
                @foreach ($elements as $element)

                    @if (is_string($element))
                        <span class="join-item btn btn-disabled" aria-disabled="true">{{ $element }}</span>
                    @endif

                    <!-- Array Of Links -->
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="join-item btn btn-active" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="join-item btn" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <!-- Next Page Link -->
                @if ($paginator->hasMorePages())
                    <a class="join-item btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Próxima">›</a>
                @else
                    <span class="join-item btn btn-disabled" aria-disabled="true" aria-label="Próxima">›</span>
                @endif
            </div>
        </nav>
    </div>
@endif
