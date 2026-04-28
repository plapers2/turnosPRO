<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <header
            class="relative bg-[#fcf9f3]/80 backdrop-blur-md border border-outline-variant/20
           rounded-xl mx-8 mt-10 mb-4 px-6 py-5 flex flex-col lg:flex-row
           items-start lg:items-center justify-between gap-4 shadow-[0_8px_20px_rgba(95,94,90,0.04)]">

            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">design_services</span>
                    </div>

                    <h2 class="text-xl font-bold text-primary tracking-tight">
                        Seleccionar Servicios
                    </h2>
                </div>

                <p class="text-sm text-on-surface-variant ml-13">
                    Elige los servicios que deseas incluir en tu cita
                </p>
            </div>
        </header>

        <!-- HERO / INFO EMPRESA -->
        <div class="relative bg-gradient-to-br from-primary/10 via-surface to-secondary/10 px-8 py-8 border-b border-outline-variant/20">
            <div class="flex items-center gap-5">
                <!-- Logo empresa -->
                <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 shadow-sm border border-outline-variant/20">
                    @if ($company->logo)
                    <img src="{{ $company->logo }}" alt="{{ $company->name }}" class="w-full h-full object-cover" />
                    @else
                    <div class="w-full h-full bg-primary/10 flex items-center justify-center">
                        <span class="text-xl font-bold text-primary/50 font-headline">
                            {{ strtoupper(substr($company->name, 0, 2)) }}
                        </span>
                    </div>
                    @endif
                </div>

                <div class="flex-1">
                    <p class="text-xs font-semibold tracking-widest uppercase text-primary mb-1 font-label">Paso 2 de 3</p>
                    <h2 class="text-2xl font-bold text-on-surface font-headline tracking-tight">
                        {{ $company->name }}
                    </h2>
                    <div class="flex flex-wrap gap-3 mt-1">
                        <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                            <span class="material-symbols-outlined text-[13px] text-primary/60">location_on</span>
                            {{ $company->address }}
                        </span>
                        <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                            <span class="material-symbols-outlined text-[13px] text-primary/60">call</span>
                            {{ $company->phone }}
                        </span>
                    </div>
                </div>

                <!-- Volver -->
                <a href="{{ route('appointment.index') }}"
                    class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors px-3 py-2 rounded-lg hover:bg-primary/5">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    Cambiar empresa
                </a>
            </div>

            <!-- Decoración -->
            <div class="absolute right-6 top-2 opacity-5 pointer-events-none select-none">
                <span class="material-symbols-outlined" style="font-size: 130px;">design_services</span>
            </div>
        </div>

        <!-- CANVAS -->
        <div class="p-8 pb-24">

            @if ($services->isEmpty())
            <!-- Estado vacío -->
            <div class="flex flex-col items-center justify-center text-center py-24 px-6
                    bg-surface-container-lowest rounded-xl border border-outline-variant/20">
                <div class="w-14 h-14 flex items-center justify-center rounded-full bg-primary/10 text-primary mb-5">
                    <span class="material-symbols-outlined">design_services</span>
                </div>
                <h3 class="text-lg font-semibold text-primary mb-2">Sin servicios disponibles</h3>
                <p class="text-sm text-on-surface-variant max-w-sm leading-relaxed">
                    Este negocio aún no tiene servicios activos. Intenta con otra empresa.
                </p>
                <a href="{{ route('appointment.index') }}"
                    class="mt-6 inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold bg-primary text-white hover:bg-primary/90 transition">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    Volver a empresas
                </a>
            </div>
            @else
            <!-- Instrucción -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-on-surface font-headline">Servicios disponibles</h3>
                    <p class="text-xs text-on-surface-variant mt-0.5">Puedes seleccionar uno o varios servicios</p>
                </div>
                <span id="selectedCount"
                    class="hidden px-3 py-1 rounded-full text-xs font-bold font-label bg-primary/10 text-primary">
                    0 seleccionados
                </span>
            </div>

            <!-- Grid servicios -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="servicesGrid">
                @foreach ($services as $service)
                <label
                    data-id="{{ $service->id }}"
                    data-price="{{ $service->price }}"
                    data-duration="{{ $service->duration }}"
                    class="service-card group relative bg-surface-container-lowest rounded-xl overflow-hidden
                                border-2 border-outline-variant/20 cursor-pointer transition-all duration-200
                                hover:border-primary/40 hover:-translate-y-0.5 hover:shadow-md">
                    <input type="checkbox" class="sr-only service-checkbox" value="{{ $service->id }}" />

                    <!-- Check indicator -->
                    <div class="check-indicator absolute top-3 right-3 z-10 w-6 h-6 rounded-full border-2
                                border-outline-variant/40 bg-surface flex items-center justify-center transition-all">
                        <span class="material-symbols-outlined text-[14px] text-white hidden check-icon">check</span>
                    </div>

                    <!-- Imagen -->
                    <div class="w-full h-40 overflow-hidden bg-surface-container">
                        @if ($service->image)
                        <img
                            src="{{ asset('storage/' . $service->image) }}"
                            alt="{{ $service->name }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary/30" style="font-size:48px">spa</span>
                        </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="p-4 flex flex-col gap-2">
                        <h4 class="text-sm font-bold text-on-surface font-headline line-clamp-1 group-hover:text-primary transition-colors">
                            {{ $service->name }}
                        </h4>
                        <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2">
                            {{ $service->description }}
                        </p>
                        <div class="flex gap-2 mt-1">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-primary-fixed text-on-primary-fixed text-[11px] font-semibold font-label">
                                <span class="material-symbols-outlined text-[12px]">schedule</span>
                                {{ $service->duration }} min
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-secondary-fixed text-on-secondary-fixed text-[11px] font-semibold font-label">
                                <span class="material-symbols-outlined text-[12px]">payments</span>
                                ${{ number_format($service->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <!-- RESUMEN + BOTÓN CONTINUAR (barra fija abajo) -->
            <div id="summaryBar"
                class="fixed bottom-0 left-0 right-0 z-30 bg-surface/95 backdrop-blur-sm border-t border-outline-variant/20
                    px-8 py-4 flex items-center justify-between gap-4 translate-y-full transition-transform duration-300">

                <div class="flex flex-col">
                    <span class="text-xs text-on-surface-variant font-label">Resumen</span>
                    <div class="flex items-center gap-4 mt-0.5">
                        <span class="text-sm font-semibold text-on-surface">
                            <span id="summaryServices">0</span> servicio(s)
                        </span>
                        <span class="text-xs text-on-surface-variant">·</span>
                        <span class="text-sm font-semibold text-on-surface">
                            <span id="summaryDuration">0</span> min
                        </span>
                        <span class="text-xs text-on-surface-variant">·</span>
                        <span class="text-sm font-bold text-primary">
                            $<span id="summaryTotal">0</span>
                        </span>
                    </div>
                </div>

                <form method="GET" action="{{ route('booking.prepareCreate') }}" id="continueForm">
                    <input type="hidden" name="company_id" value="{{ $company->id }}" />
                    <div id="serviceInputs"></div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold
                            bg-primary text-white hover:bg-primary/90 transition shadow-sm">
                        Continuar
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </form>
            </div>
            @endif
        </div>

    </main>
</x-app-layout>

<script>
    const cards = document.querySelectorAll('.service-card');
    const summaryBar = document.getElementById('summaryBar');
    const summaryServices = document.getElementById('summaryServices');
    const summaryDuration = document.getElementById('summaryDuration');
    const summaryTotal = document.getElementById('summaryTotal');
    const serviceInputs = document.getElementById('serviceInputs');
    const selectedCount = document.getElementById('selectedCount');

    cards.forEach(card => {
        card.addEventListener('click', () => {
            const checkbox = card.querySelector('.service-checkbox');
            const indicator = card.querySelector('.check-indicator');
            const checkIcon = card.querySelector('.check-icon');

            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                card.classList.add('border-primary', 'shadow-md');
                card.classList.remove('border-outline-variant/20');
                indicator.classList.add('bg-primary', 'border-primary');
                indicator.classList.remove('border-outline-variant/40', 'bg-surface');
                checkIcon.classList.remove('hidden');
            } else {
                card.classList.remove('border-primary', 'shadow-md');
                card.classList.add('border-outline-variant/20');
                indicator.classList.remove('bg-primary', 'border-primary');
                indicator.classList.add('border-outline-variant/40', 'bg-surface');
                checkIcon.classList.add('hidden');
            }

            updateSummary();
        });
    });

    function updateSummary() {
        const checked = document.querySelectorAll('.service-checkbox:checked');
        let totalPrice = 0;
        let totalDuration = 0;

        // Limpiar inputs ocultos
        serviceInputs.innerHTML = '';

        checked.forEach(cb => {
            const card = cb.closest('.service-card');
            totalPrice += parseFloat(card.dataset.price);
            totalDuration += parseInt(card.dataset.duration);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'services[]';
            input.value = card.dataset.id;
            serviceInputs.appendChild(input);
        });

        summaryServices.textContent = checked.length;
        summaryDuration.textContent = totalDuration;
        summaryTotal.textContent = new Intl.NumberFormat('es-CO').format(totalPrice);

        // Mostrar/ocultar barra
        if (checked.length > 0) {
            summaryBar.classList.remove('translate-y-full');
            selectedCount.classList.remove('hidden');
            selectedCount.textContent = checked.length + ' seleccionado' + (checked.length > 1 ? 's' : '');
        } else {
            summaryBar.classList.add('translate-y-full');
            selectedCount.classList.add('hidden');
        }
    }
</script>