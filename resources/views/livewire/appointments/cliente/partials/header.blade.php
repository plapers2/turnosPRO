<header
    class="relative mx-0 mb-5 overflow-hidden rounded-2xl border border-outline-variant/30
           bg-surface-container-lowest px-6 py-7
           flex items-center justify-between gap-4
           shadow-[0_1px_8px_rgba(95,94,90,0.06)]">

    <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-primary rounded-l-2xl"></div>

    <div class="flex items-center gap-4 pl-2">
        <div class="flex h-[42px] w-[42px] shrink-0 items-center justify-center
                    rounded-xl border border-primary-fixed-dim/40 bg-primary-fixed/20 text-primary">
            <span class="material-symbols-outlined text-[20px]">calendar_month</span>
        </div>
        <div class="flex flex-col gap-0.5">
            <h2 class="text-[17px] font-semibold leading-tight tracking-tight text-on-surface">
                Mis Citas
            </h2>
            <p class="text-[13px] text-on-surface-variant">
                Consulta y gestiona tus reservas agendadas.
            </p>
        </div>
    </div>

    <a href="{{ route('appointment.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold
               bg-primary text-on-primary hover:opacity-90 transition-opacity shrink-0">
        <span class="material-symbols-outlined text-[16px]">add</span>
        Nueva cita
    </a>
</header>