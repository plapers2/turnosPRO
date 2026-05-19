<x-app-layout>
    <main class="flex-1 flex flex-col relative bg-surface">

        {{-- HERO --}}
        <div class="relative bg-surface px-8 py-8 border-b border-outline-variant/60">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold tracking-widest uppercase text-primary mb-2 font-label">Reservar cita</p>
                <h2 class="text-2xl font-bold text-on-surface font-headline tracking-tight">
                    Agenda tu cita en un solo paso
                </h2>
                <p class="text-on-surface-variant text-sm leading-relaxed mt-1">
                    Selecciona la empresa, servicios, fecha y profesional — todo desde aquí.
                </p>
            </div>
            <div class="absolute right-8 top-4 opacity-10 pointer-events-none select-none">
                <span class="material-symbols-outlined" style="font-size:140px">calendar_add_on</span>
            </div>
        </div>

        <livewire:appointments.appointment-create :company-id="request('company')" />

    </main>
</x-app-layout>