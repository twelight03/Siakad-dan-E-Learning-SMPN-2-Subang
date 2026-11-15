@if ($paginator->total() > 0)
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-slate-600">
        <!-- Left: Per-Page Selector & Summary Info -->
        <div class="flex flex-wrap items-center gap-2.5">
            <span class="text-slate-500 font-medium">Tampilkan:</span>
            <form method="GET" action="{{ url()->current() }}" class="inline-flex items-center">
                @foreach(request()->except(['per_page', 'page']) as $key => $val)
                    @if(is_array($val))
                        @foreach($val as $subVal)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $subVal }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endif
                @endforeach
                @php
                    $currentPerPage = (int) request('per_page', $paginator->perPage());
                @endphp
                <select name="per_page" onchange="this.form.submit()" class="text-xs py-1 px-2.5 rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 bg-slate-50 font-bold text-slate-700 shadow-xs cursor-pointer">
                    <option value="10" {{ $currentPerPage == 10 ? 'selected' : '' }}>10 data</option>
                    <option value="25" {{ $currentPerPage == 25 ? 'selected' : '' }}>25 data</option>
                    <option value="50" {{ $currentPerPage == 50 ? 'selected' : '' }}>50 data</option>
                    <option value="100" {{ $currentPerPage == 100 ? 'selected' : '' }}>100 data</option>
                </select>
            </form>
            <span class="text-slate-300 hidden sm:inline">|</span>
            <p class="text-xs text-slate-500">
                Menampilkan <span class="font-bold text-slate-700">{{ $paginator->firstItem() ?? 0 }}</span> &ndash; <span class="font-bold text-slate-700">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-700">{{ $paginator->total() }}</span> data
            </p>
        </div>

        <!-- Right: Pagination Links -->
        @if ($paginator->hasPages())
            <div class="flex items-center space-x-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed">
                        &laquo; Prev
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-blue-600 transition shadow-xs">
                        &laquo; Prev
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-2 py-1 text-slate-400 text-xs font-bold">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 shadow-xs">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-blue-600 transition shadow-xs">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-blue-600 transition shadow-xs">
                        Next &raquo;
                    </a>
                @else
                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed">
                        Next &raquo;
                    </span>
                @endif
            </div>
        @endif
    </nav>
@endif
