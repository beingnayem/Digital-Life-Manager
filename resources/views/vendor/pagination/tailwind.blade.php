@if ($paginator->hasPages())
    <div class="pagination-shell">
        <div class="pagination-info">
            @if ($paginator->onFirstPage())
                Showing {{ $paginator->count() }} of {{ $paginator->total() }}
            @else
                Showing {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} of {{ $paginator->total() }}
            @endif
        </div>

        <nav role="navigation" aria-label="Pagination Navigation">
            <span class="inline-flex overflow-hidden rounded-xl border border-slate-200 bg-white">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-2 text-sm text-slate-400">Previous</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Previous</a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-3 py-2 text-sm text-slate-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="border-l border-slate-200 bg-primary-600 px-3 py-2 text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="border-l border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="border-l border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Next</a>
                @else
                    <span class="border-l border-slate-200 px-3 py-2 text-sm text-slate-400">Next</span>
                @endif
            </span>
        </nav>
    </div>
@endif