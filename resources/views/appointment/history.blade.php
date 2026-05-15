<x-app-layout>
    <main class="flex-1 flex flex-col relative bg-surface">

        <!-- HERO -->
        <div class="relative bg-surface px-8 py-10 border-b border-outline-variant/60">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold tracking-widest uppercase text-primary mb-2 font-label">Mi cuenta</p>
                <h2 class="text-3xl font-bold text-on-surface font-headline tracking-tight mb-2">
                    Mis Citas
                </h2>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Consulta tus citas próximas e historial de reservas.
                </p>
            </div>
            <div class="absolute right-8 top-4 opacity-10 pointer-events-none select-none">
                <span class="material-symbols-outlined" style="font-size: 160px;">calendar_month</span>
            </div>
        </div>

        <div class="p-8 pb-20 space-y-10">

            {{-- PRÓXIMAS CITAS --}}
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-primary text-[18px]">upcoming</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-on-surface font-headline tracking-tight">Próximas citas</h3>
                        <p class="text-xs text-on-surface-variant font-label">{{ $proximas->count() }} {{ $proximas->count() === 1 ? 'cita activa' : 'citas activas' }}</p>
                    </div>
                    <div class="flex-1 h-px bg-outline-variant/30 ml-2"></div>
                </div>

                @forelse ($proximas as $cita)
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
                    shadow-[0_4px_20px_rgba(95,94,90,0.07)] p-6 mb-4
                    hover:shadow-[0_8px_30px_rgba(95,94,90,0.12)] transition-all duration-200">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                        <!-- INFO CITA -->
                        <div class="flex items-start gap-4">

                            <!-- Fecha box -->
                            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-primary/10 flex flex-col items-center justify-center">
                                <span class="text-xs font-bold text-primary uppercase">
                                    {{ $cita->start_time->format('M') }}
                                </span>
                                <span class="text-xl font-black text-primary leading-none">
                                    {{ $cita->start_time->format('d') }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-base font-bold text-on-surface">
                                        {{ $cita->company->name }}
                                    </span>
                                    @php
                                    $statusClasses = match($cita->status) {
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'confirmed' => 'bg-green-100 text-green-700',
                                    default => 'bg-gray-100 text-gray-600',
                                    };
                                    $statusLabel = match($cita->status) {
                                    'pending' => 'Pendiente',
                                    'confirmed' => 'Confirmada',
                                    default => ucfirst($cita->status),
                                    };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClasses }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <!-- Servicios -->
                                <div class="flex flex-wrap gap-1.5 mt-1">
                                    @foreach ($cita->services as $servicio)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-primary-fixed text-on-primary-fixed text-[11px] font-semibold font-label">
                                        <span class="material-symbols-outlined text-[11px]">spa</span>
                                        {{ $servicio->name }}
                                        · ${{ number_format($servicio->price, 0, ',', '.') }}
                                    </span>
                                    @endforeach
                                </div>

                                <!-- Profesional + hora -->
                                <div class="flex flex-wrap gap-4 mt-1">
                                    <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[13px] text-primary/60">person</span>
                                        {{ $cita->user->name }}
                                    </span>
                                    <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[13px] text-primary/60">schedule</span>
                                        {{ $cita->start_time->format('H:i') }} – {{ $cita->end_time->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- ACCIÓN CANCELAR -->
                        @if ($cita->isCancellable() && now()->lt($cita->start_time->subHours(2)))
                        <button
                            data-id="{{ $cita->id }}"
                            data-empresa="{{ $cita->company->name }}"
                            data-fecha="{{ $cita->start_time->format('d/m/Y H:i') }}"
                            onclick="confirmarCancelacion(this)"
                            class="flex-shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold text-error border border-error/30 hover:bg-error/10 transition">
                            <span class="material-symbols-outlined text-[15px]">cancel</span>
                            Cancelar cita
                        </button>
                        @elseif ($cita->isCancellable())
                        <span class="flex-shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold
                            text-on-surface-variant border border-outline-variant/30 cursor-not-allowed opacity-60"
                            title="No se puede cancelar con menos de 2 horas de anticipación">
                            <span class="material-symbols-outlined text-[15px]">lock</span>
                            Cancelación bloqueada
                        </span>
                        @endif

                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center text-center py-16 px-6
                    bg-surface-container-lowest rounded-xl border border-outline-variant/20">
                    <span class="material-symbols-outlined text-4xl text-primary/30 mb-3">event_busy</span>
                    <p class="text-sm font-semibold text-on-surface mb-1">No tienes citas próximas</p>
                    <p class="text-xs text-on-surface-variant mb-4">¡Agenda una nueva cita cuando quieras!</p>
                    <a href="{{ route('appointment.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold bg-primary text-white hover:bg-primary/90 transition">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Reservar cita
                    </a>
                </div>
                @endforelse
            </section>

            {{-- HISTORIAL --}}
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-secondary/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-secondary text-[18px]">history</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-on-surface font-headline tracking-tight">Historial</h3>
                        <p class="text-xs text-on-surface-variant font-label">{{ $historicas->count() }} {{ $historicas->count() === 1 ? 'cita registrada' : 'citas registradas' }}</p>
                    </div>
                    <div class="flex-1 h-px bg-outline-variant/30 ml-2"></div>
                </div>

                @forelse ($historicas as $cita)
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-5 mb-3 opacity-80">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">

                        <div class="flex items-start gap-4">

                            <!-- Fecha box apagada -->
                            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-surface-container flex flex-col items-center justify-center">
                                <span class="text-xs font-bold text-on-surface-variant uppercase">
                                    {{ $cita->start_time->format('M') }}
                                </span>
                                <span class="text-xl font-black text-on-surface-variant leading-none">
                                    {{ $cita->start_time->format('d') }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-on-surface">
                                        {{ $cita->company->name }}
                                    </span>
                                    @php
                                    $statusClasses = match($cita->status) {
                                    'completed' => 'bg-blue-100 text-blue-700',
                                    'cancelled' => 'bg-red-100 text-red-600',
                                    default => 'bg-gray-100 text-gray-500',
                                    };
                                    $statusLabel = match($cita->status) {
                                    'completed' => 'Completada',
                                    'cancelled' => 'Cancelada',
                                    default => ucfirst($cita->status),
                                    };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClasses }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-1.5 mt-1">
                                    @foreach ($cita->services as $servicio)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-surface-container text-on-surface-variant text-[11px] font-semibold font-label">
                                        {{ $servicio->name }} · ${{ number_format($servicio->price, 0, ',', '.') }}
                                    </span>
                                    @endforeach
                                </div>

                                <div class="flex flex-wrap gap-4 mt-1">
                                    <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[13px]">person</span>
                                        {{ $cita->user->name }}
                                    </span>
                                    <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[13px]">schedule</span>
                                        {{ $cita->start_time->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center text-center py-12 px-6
                    bg-surface-container-lowest rounded-xl border border-outline-variant/20">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant/30 mb-3">history_toggle_off</span>
                    <p class="text-sm text-on-surface-variant">No tienes citas anteriores registradas.</p>
                </div>
                @endforelse
            </section>

        </div>
    </main>
</x-app-layout>

<script>
    function confirmarCancelacion(btn) {
        const id = btn.dataset.id;
        const empresa = btn.dataset.empresa;
        const fecha = btn.dataset.fecha;

        Swal.fire({
            title: '¿Cancelar esta cita?',
            html: `
            <div class="text-left space-y-2 mt-2">
                <p class="text-sm text-gray-600">Estás a punto de cancelar tu cita en:</p>
                <p class="font-semibold text-gray-800">${empresa}</p>
                <p class="text-sm text-gray-500">${fecha}</p>
                <p class="text-xs text-red-500 mt-3">Esta acción no se puede deshacer.</p>
            </div>
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ba1a1a',
            cancelButtonColor: '#847467',
            confirmButtonText: 'Sí, cancelar cita',
            cancelButtonText: 'Volver',
            reverseButtons: true,
            background: '#fcf9f3',
            color: '#1c1c19',
            customClass: {
                popup: 'rounded-xl shadow-lg',
                confirmButton: 'px-4 py-2 rounded-lg font-semibold',
                cancelButton: 'px-4 py-2 rounded-lg'
            },
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Cancelando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                background: '#fcf9f3',
                color: '#1c1c19',
            });

            try {
                const res = await fetch(`/my-appointments/cancel/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        title: '¡Cita cancelada!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#fcf9f3',
                        color: '#1c1c19',
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({
                        title: 'No se pudo cancelar',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#ba1a1a',
                        background: '#fcf9f3',
                        color: '#1c1c19',
                    });
                }
            } catch (e) {
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error inesperado. Intenta de nuevo.',
                    icon: 'error',
                    confirmButtonColor: '#ba1a1a',
                    background: '#fcf9f3',
                    color: '#1c1c19',
                });
            }
        });
    }
</script>