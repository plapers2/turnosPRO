@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
        class="flex items-center justify-between gap-3 flex-wrap">

        {{-- Info --}}
        <p class="text-[12.5px] text-on-surface-variant">
            Mostrando
            <strong
                class="font-semibold text-on-surface">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</strong>
            de
            <strong class="font-semibold text-on-surface">{{ $paginator->total() }}</strong>
            resultados
        </p>

        {{-- Botones --}}
        <div class="flex items-center gap-1">

            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center justify-center w-16 p-5 h-10 rounded-[10px]
                             border border-transparent text-on-surface-variant opacity-35 cursor-not-allowed"
                    aria-disabled="true">
                    <span class="material-symbols-outlined text-[17px]">chevron_left</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-[10px]
                          border border-transparent text-on-surface-variant
                          transition-all hover:bg-surface-container hover:border-outline-variant hover:text-on-surface"
                    aria-label="{{ __('pagination.previous') }}">
                    <span class="material-symbols-outlined text-[17px]">chevron_left</span>
                </a>
            @endif

            {{-- Números --}}
            @foreach ($elements as $element)
                {{-- Puntos suspensivos --}}
                @if (is_string($element))
                    <span class="text-[13px] text-outline px-1 select-none">···</span>
                @endif

                {{-- Páginas --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="inline-flex items-center justify-center min-w-10 h-8
                                         rounded-[10px] bg-primary-container text-on-primary-container
                                         text-sm font-bold">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="inline-flex items-center justify-center min-w-10 h-8
                                      rounded-[10px] border border-transparent text-on-surface-variant
                                      text-[12.5px] font-medium transition-all
                                      hover:bg-surface-container hover:border-outline-variant hover:text-on-surface">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-[10px]
                          border border-transparent text-on-surface-variant
                          transition-all hover:bg-surface-container hover:border-outline-variant hover:text-on-surface"
                    aria-label="{{ __('pagination.next') }}">
                    <span class="material-symbols-outlined text-[17px]">chevron_right</span>
                </a>
            @else
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-[10px]
                             border border-transparent text-on-surface-variant opacity-35 cursor-not-allowed"
                    aria-disabled="true">
                    <span class="material-symbols-outlined text-[17px]">chevron_right</span>
                </span>
            @endif

        </div>
    </nav>
@endif
