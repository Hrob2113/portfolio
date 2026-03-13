<x-filament-panels::page>

    {{-- Header bar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
        <div style="display:flex;align-items:center;gap:.75rem">
            <a href="{{ \App\Filament\Resources\WorkResource::getUrl('index') }}"
               style="font-size:.8125rem;color:var(--gray-400);text-decoration:none"
               onmouseover="this.style.color='var(--gray-100)'" onmouseout="this.style.color='var(--gray-400)'">
                ← Works list
            </a>
            <span style="color:var(--gray-700)">|</span>
            <span style="font-size:.8125rem;color:var(--gray-500)">
                {{ $works->where('published', true)->count() }} published &middot; {{ $works->count() }} total
            </span>
        </div>
        <a href="{{ \App\Filament\Resources\WorkResource::getUrl('create') }}"
           style="font-size:.8125rem;color:var(--primary-400);text-decoration:none">
            + New work
        </a>
    </div>

    @if ($works->isEmpty())
        <div style="text-align:center;padding:4rem 0;color:var(--gray-500);font-size:.875rem">
            No works yet.
            <a href="{{ \App\Filament\Resources\WorkResource::getUrl('create') }}" style="color:var(--primary-400)">Create the first one →</a>
        </div>
    @else

    {{-- Category filter --}}
    <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap">
        @php $categories = $works->pluck('category')->unique()->sort()->values(); @endphp
        <button onclick="filterWorks('all')" id="f-all"
                class="f-btn f-btn--active"
                style="padding:.3rem .85rem;border-radius:999px;font-size:.75rem;font-family:inherit;cursor:pointer;transition:all .2s;border:1px solid var(--primary-500);background:var(--primary-500);color:#fff">
            All ({{ $works->where('published', true)->count() }})
        </button>
        @foreach ($categories as $cat)
            <button onclick="filterWorks('{{ $cat }}')" id="f-{{ $cat }}"
                    style="padding:.3rem .85rem;border-radius:999px;font-size:.75rem;font-family:inherit;cursor:pointer;transition:all .2s;border:1px solid var(--gray-600);background:transparent;color:var(--gray-400)">
                {{ \App\Models\Work::CATEGORIES[$cat] ?? $cat }}
                ({{ $works->where('published', true)->where('category', $cat)->count() }})
            </button>
        @endforeach
    </div>

    {{-- Live grid preview — 12-col matching the frontend exactly --}}
    <div id="preview-grid" style="display:grid;grid-template-columns:repeat(12,1fr);grid-auto-rows:140px;gap:6px;">
        @foreach ($works as $work)
            @php
                $gridStyle = match($work->layout) {
                    'pc--featured' => 'grid-column:span 7;grid-row:span 3',
                    'pc--tall'     => 'grid-column:span 5;grid-row:span 3',
                    'pc--wide'     => 'grid-column:span 8;grid-row:span 2',
                    'pc--wide2'    => 'grid-column:span 7;grid-row:span 2',
                    'pc--sq2'      => 'grid-column:span 5;grid-row:span 2',
                    'pc--half'     => 'grid-column:span 6;grid-row:span 2',
                    default        => 'grid-column:span 4;grid-row:span 2',
                };
                $catColor = match($work->category) {
                    'web'     => '#60a5fa',
                    'ui'      => '#34d399',
                    'graphic' => '#fbbf24',
                    'brand'   => '#f87171',
                    default   => '#9ca3af',
                };
            @endphp
            <div class="pv-card"
                 data-cat="{{ $work->category }}"
                 data-published="{{ $work->published ? '1' : '0' }}"
                 style="{{ $gridStyle }};position:relative;overflow:hidden;border-radius:10px;
                        border:1px solid var(--gray-700);background:var(--gray-900);
                        cursor:pointer;transition:transform .25s,border-color .2s,opacity .3s;
                        {{ $work->published ? '' : 'opacity:.35;' }}">

                {{-- Image --}}
                @if ($work->imageUrl())
                    <img src="{{ $work->imageUrl() }}" alt="{{ $work->title }}"
                         style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;
                                filter:grayscale(15%) brightness(.72);transition:filter .3s">
                @else
                    <div style="position:absolute;inset:0;background:linear-gradient(135deg,#1a2830,#0d1a22)"></div>
                @endif

                {{-- Gradient overlay --}}
                <div class="pv-overlay"
                     style="position:absolute;inset:0;background:linear-gradient(to top,rgba(4,14,20,.97) 0%,rgba(4,14,20,.5) 55%,transparent 100%);
                            opacity:0;transition:opacity .25s;z-index:1"></div>

                {{-- Top badges --}}
                <div style="position:absolute;top:8px;left:8px;display:flex;gap:5px;z-index:3">
                    <span style="font-size:.58rem;padding:.18rem .42rem;border-radius:4px;font-family:monospace;
                                 background:rgba(0,0,0,.65);backdrop-filter:blur(6px);
                                 color:{{ $catColor }};border:1px solid {{ $catColor }}40">
                        {{ $work->category }}
                    </span>
                    @if (! $work->published)
                        <span style="font-size:.58rem;padding:.18rem .42rem;border-radius:4px;font-family:monospace;
                                     background:rgba(0,0,0,.65);color:#6b7280;border:1px solid #6b728040">draft</span>
                    @endif
                </div>

                {{-- Sort order --}}
                <div style="position:absolute;top:8px;right:8px;z-index:3;font-size:.58rem;padding:.18rem .42rem;
                            border-radius:4px;background:rgba(0,0,0,.65);backdrop-filter:blur(6px);
                            color:var(--gray-500);font-family:monospace">
                    #{{ $work->sort_order }}
                </div>

                {{-- Info panel (on hover) --}}
                <div class="pv-info"
                     style="position:absolute;bottom:0;left:0;right:0;padding:.65rem .75rem;z-index:2;
                            transform:translateY(6px);opacity:0;transition:transform .25s,opacity .25s">
                    <div style="font-size:.6rem;color:rgba(255,255,255,.5);margin-bottom:.15rem">{{ $work->category_label }}</div>
                    <div style="font-size:.78rem;font-weight:600;color:#fff;line-height:1.25;margin-bottom:.3rem">{{ $work->title }}</div>
                    @if ($work->tags)
                        <div style="display:flex;flex-wrap:wrap;gap:3px;margin-bottom:.4rem">
                            @foreach ($work->tags as $tag)
                                <span style="font-size:.57rem;padding:.12rem .3rem;border-radius:3px;
                                             background:rgba(255,255,255,.09);color:rgba(255,255,255,.6)">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <span style="font-size:.6rem;color:rgba(255,255,255,.35)">{{ $work->year }}</span>
                        <div style="display:flex;gap:.4rem">
                            <a href="{{ \App\Filament\Resources\WorkResource::getUrl('edit', ['record' => $work]) }}"
                               style="font-size:.62rem;color:var(--primary-300);text-decoration:none;
                                      padding:.15rem .45rem;border-radius:4px;
                                      border:1px solid var(--primary-800);background:rgba(26,80,96,.35)"
                               onclick="event.stopPropagation()">Edit</a>
                            @if ($work->link)
                                <a href="{{ $work->link }}" target="_blank" rel="noopener noreferrer"
                                   style="font-size:.62rem;color:rgba(255,255,255,.4);text-decoration:none;
                                          padding:.15rem .45rem;border-radius:4px;border:1px solid var(--gray-700)"
                                   onclick="event.stopPropagation()">↗</a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Layout chip --}}
                <div style="position:absolute;bottom:8px;right:8px;z-index:1;
                            font-size:.53rem;padding:.12rem .35rem;border-radius:3px;
                            background:rgba(0,0,0,.55);color:var(--gray-700);font-family:monospace">
                    {{ $work->layout }}
                </div>

            </div>
        @endforeach
    </div>

    {{-- Layout reference legend --}}
    <div style="margin-top:2rem;padding:1rem 1.25rem;border-radius:10px;border:1px solid var(--gray-700);background:var(--gray-800)">
        <div style="font-size:.65rem;color:var(--gray-500);margin-bottom:.75rem;text-transform:uppercase;letter-spacing:.09em">
            Layout reference — 12-column grid
        </div>
        <div style="display:flex;flex-direction:column;gap:5px">
            @foreach (\App\Models\Work::LAYOUTS as $cls => $label)
                @php
                    $cols = match($cls) {
                        'pc--featured' => 7,
                        'pc--tall'     => 5,
                        'pc--wide'     => 8,
                        'pc--wide2'    => 7,
                        'pc--sq2'      => 5,
                        'pc--half'     => 6,
                        default        => 4,
                    };
                    $remainder = 12 - $cols;
                    $usedBy = $works->where('layout', $cls)->count();
                @endphp
                <div style="display:grid;grid-template-columns:repeat(12,1fr);gap:3px;height:22px">
                    <div style="grid-column:span {{ $cols }};background:var(--primary-900);
                                border:1px solid var(--primary-800);border-radius:3px;
                                display:flex;align-items:center;justify-content:space-between;
                                padding:0 .4rem">
                        <span style="font-size:.58rem;color:var(--primary-300);font-family:monospace">{{ $cls }}</span>
                        <span style="font-size:.55rem;color:var(--primary-500)">({{ $cols }}/12)</span>
                    </div>
                    <div style="grid-column:span {{ $remainder }};background:var(--gray-900);
                                border:1px dashed var(--gray-800);border-radius:3px;
                                display:flex;align-items:center;justify-content:flex-end;padding-right:.35rem">
                        @if ($usedBy > 0)
                            <span style="font-size:.55rem;color:var(--gray-600)">{{ $usedBy }} work{{ $usedBy !== 1 ? 's' : '' }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @endif

    <style>
        .pv-card:hover .pv-overlay { opacity: 1 !important; }
        .pv-card:hover .pv-info    { transform: translateY(0) !important; opacity: 1 !important; }
        .pv-card:hover             { transform: scale(1.015); border-color: var(--primary-600) !important; }
        .pv-card:hover img         { filter: grayscale(0%) brightness(.85) !important; }
        .pv-card.dim               { opacity: .12 !important; pointer-events: none; }
    </style>

    <script>
        function filterWorks(cat) {
            // Reset all buttons
            document.querySelectorAll('[id^="f-"]').forEach(btn => {
                btn.style.background = 'transparent';
                btn.style.color = 'var(--gray-400)';
                btn.style.borderColor = 'var(--gray-600)';
            });
            // Highlight active
            const btn = document.getElementById('f-' + cat);
            if (btn) {
                btn.style.background = 'var(--primary-500)';
                btn.style.color = '#fff';
                btn.style.borderColor = 'var(--primary-500)';
            }
            // Dim non-matching cards
            document.querySelectorAll('.pv-card').forEach(card => {
                const match = cat === 'all' || card.dataset.cat === cat;
                card.classList.toggle('dim', !match);
            });
        }
    </script>

</x-filament-panels::page>
