<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HERO -->
        <div class="relative bg-gradient-to-br from-primary/10 via-surface to-secondary/10 px-8 py-10 border-b border-outline-variant/20">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold tracking-widest uppercase text-primary mb-2 font-label">Paso 1 de 3</p>
                <h2 class="text-3xl font-bold text-on-surface font-headline tracking-tight mb-2">
                    ¿A dónde quieres ir?
                </h2>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Explora los negocios disponibles, filtra por categoría y selecciona el que mejor se adapte a lo que necesitas.
                </p>
            </div>

            <!-- Decoración fondo -->
            <div class="absolute right-8 top-4 opacity-5 pointer-events-none select-none">
                <span class="material-symbols-outlined" style="font-size: 160px;">store</span>
            </div>
        </div>

        <!-- BUSCADOR + FILTRO RÁPIDO -->
        <div class="sticky top-0 z-20 bg-surface/95 backdrop-blur-sm border-b border-outline-variant/20 px-8 py-3 flex flex-col sm:flex-row items-start sm:items-center gap-3">

            <!-- Búsqueda -->
            <div class="relative flex-1 max-w-sm">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Buscar company..."
                    class="w-full pl-9 pr-4 py-2 rounded-lg bg-surface-container text-sm text-on-surface placeholder:text-on-surface-variant border border-outline-variant/30 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
            </div>

            <!-- Filtros por categoría (pills) -->
            <div class="flex flex-wrap gap-2" id="categoryFilters">
                <button
                    data-category="all"
                    class="category-pill active px-3 py-1.5 rounded-full text-xs font-semibold font-label transition-all border
                    bg-primary text-on-primary border-primary">
                    Todos
                </button>
                @foreach ($tiposNegocio as $tipo)
                @if ($tipo->companies->count() > 0)
                <button
                    data-category="{{ $tipo->id }}"
                    class="category-pill px-3 py-1.5 rounded-full text-xs font-semibold font-label transition-all border
                            bg-surface-container text-on-surface-variant border-outline-variant/30 hover:border-primary hover:text-primary">
                    {{ $tipo->name }}
                </button>
                @endif
                @endforeach
            </div>
        </div>

        <!-- CANVAS -->
        <div class="p-8 pb-20 space-y-12" id="companiesCanvas">

            @forelse ($tiposNegocio as $tipo)
            @if ($tipo->companies->count() > 0)
            <!-- SECCIÓN POR CATEGORÍA -->
            <section data-section="{{ $tipo->id }}" class="category-section">

                <!-- Título categoría -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-primary text-[18px]">storefront</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-on-surface font-headline tracking-tight">
                            {{ $tipo->name }}
                        </h3>
                        <p class="text-xs text-on-surface-variant font-label">
                            {{ $tipo->companies->count() }} {{ $tipo->companies->count() === 1 ? 'negocio' : 'negocios' }} disponibles
                        </p>
                    </div>
                    <div class="flex-1 h-px bg-outline-variant/30 ml-2"></div>
                </div>

                <!-- Grid de cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($tipo->companies as $company)
                    <a
                        href="{{ route('appointments.selectServices', $company->id) }}"
                        data-name="{{ strtolower($company->name) }}"
                        class="company-card group bg-surface-container-lowest rounded-xl flex flex-col shadow-[0_4px_20px_rgba(95,94,90,0.07)] hover:shadow-[0_8px_30px_rgba(95,94,90,0.13)] transition-all duration-300 hover:-translate-y-1 border border-outline-variant/10 hover:border-primary/30 cursor-pointer overflow-hidden">
                        <!-- Logo / Banner -->
                        <div class="relative w-full h-36 overflow-hidden bg-surface-container flex items-center justify-center">
                            @if ($company->logo)
                            <img
                                src="{{ asset('storage/' . $company->logo) }}"
                                alt="{{ $company->name }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            @else
                            <!-- Placeholder con iniciales -->
                            <div class="w-full h-full bg-gradient-to-br from-primary/15 to-secondary/10 flex items-center justify-center">
                                <span class="text-4xl font-bold text-primary/40 font-headline select-none">
                                    {{ strtoupper(substr($company->name, 0, 2)) }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="p-5 flex flex-col gap-3 flex-1">
                            <h4 class="text-base font-bold text-on-surface font-headline tracking-tight group-hover:text-primary transition-colors line-clamp-1">
                                {{ $company->name }}
                            </h4>

                            @if ($company->descripcion)
                            <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2">
                                {{ $company->descripcion }}
                            </p>
                            @endif

                            <!-- Detalles -->
                            <div class="flex flex-col gap-1.5 mt-auto">
                                <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[14px] text-primary/60">location_on</span>
                                    <span class="line-clamp-1">{{ $company->address }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[14px] text-primary/60">call</span>
                                    <span>{{ $company->phone }}</span>
                                </div>
                            </div>

                            <!-- CTA -->
                            <div class="flex items-center justify-between pt-3 mt-1 border-t border-outline-variant/20">
                                <span class="text-xs font-semibold text-primary font-label">
                                    Ver servicios
                                </span>
                                <span class="material-symbols-outlined text-primary text-[18px] transition-transform duration-200 group-hover:translate-x-1">
                                    arrow_forward
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>
            @endif
            @empty
            <!-- Estado vacío -->
            <div class="col-span-full flex flex-col items-center justify-center text-center py-24 px-6
                    bg-surface-container-lowest rounded-xl border border-outline-variant/20
                    shadow-[0_10px_30px_rgba(95,94,90,0.04)]">
                <div class="w-16 h-16 flex items-center justify-center rounded-full bg-primary/10 text-primary mb-6">
                    <span class="material-symbols-outlined">store_off</span>
                </div>
                <h3 class="text-xl font-semibold text-primary mb-2">No hay companies disponibles</h3>
                <p class="text-sm text-on-surface-variant max-w-md leading-relaxed">
                    Aún no hay negocios registrados en el sistema. Intenta más tarde.
                </p>
            </div>
            @endforelse

            <!-- Mensaje sin resultados (búsqueda) -->
            <div id="emptySearch" class="hidden flex-col items-center justify-center text-center py-20 px-6
                bg-surface-container-lowest rounded-xl border border-outline-variant/20">
                <span class="material-symbols-outlined text-on-surface-variant text-5xl mb-4">search_off</span>
                <p class="text-base font-semibold text-on-surface mb-1">Sin resultados</p>
                <p class="text-sm text-on-surface-variant">Intenta con otro término de búsqueda.</p>
            </div>

        </div>
    </main>
</x-app-layout>

<script>
    // ─── Filtro por categoría ──────────────────────────────────────────────
    const pills = document.querySelectorAll('.category-pill');
    const sections = document.querySelectorAll('.category-section');
    const searchInput = document.getElementById('searchInput');

    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            pills.forEach(p => {
                p.classList.remove('bg-primary', 'text-on-primary', 'border-primary', 'active');
                p.classList.add('bg-surface-container', 'text-on-surface-variant', 'border-outline-variant/30');
            });

            pill.classList.add('bg-primary', 'text-on-primary', 'border-primary', 'active');
            pill.classList.remove('bg-surface-container', 'text-on-surface-variant', 'border-outline-variant/30');

            const category = pill.dataset.category;

            sections.forEach(section => {
                if (category === 'all' || section.dataset.section === category) {
                    section.classList.remove('hidden');
                } else {
                    section.classList.add('hidden');
                }
            });

            // Limpiar búsqueda al cambiar categoría
            searchInput.value = '';
            filterBySearch('');
        });
    });

    // ─── Búsqueda en tiempo real ───────────────────────────────────────────
    searchInput.addEventListener('input', e => filterBySearch(e.target.value));

    function filterBySearch(query) {
        const q = query.toLowerCase().trim();
        const cards = document.querySelectorAll('.company-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.dataset.name || '';
            const match = !q || name.includes(q);
            card.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        sections.forEach(section => {
            if (section.classList.contains('hidden')) return;

            const visibleCards = [...section.querySelectorAll('.company-card')]
                .filter(c => c.style.display !== 'none');
            section.style.display = visibleCards.length > 0 ? '' : 'none';
        });

        document.getElementById('emptySearch').style.display = visibleCount === 0 ? 'flex' : 'none';
    }
</script>