@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginacion" class="pagination-shell">
        <p class="pagination-summary">
            Mostrando {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
        </p>

        <div class="pagination-list">
            @if ($paginator->onFirstPage())
                <span class="pagination-disabled">
                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-link" rel="prev">
                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                    Anterior
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-disabled">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-current" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-link" rel="next">
                    Siguiente
                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                </a>
            @else
                <span class="pagination-disabled">
                    Siguiente
                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
