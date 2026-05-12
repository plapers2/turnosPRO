<x-guest-layout>
    {{-- Encabezado --}}
    <div class="flex flex-col gap-1">
        <h2 class="text-lg font-semibold text-on-surface tracking-tight">
            Selecciona tu empresa
        </h2>
        <p class="text-sm text-on-surface-variant">
            Elige la empresa con la que deseas continuar
        </p>
    </div>

    {{-- Formulario --}}
    <form method="POST" action="{{ route('company.select.store') }}" class="flex flex-col gap-5">
        @csrf

        <div class="flex flex-col gap-1.5">
            <label for="company_id" class="text-xs font-medium uppercase tracking-widest text-on-surface-variant">
                Empresa
            </label>

            <div class="relative">
                <select id="company_id" name="company_id" required
                    class="w-full px-4 py-3 pr-10 rounded-xl text-sm text-on-surface
                               bg-surface-container border border-outline-variant
                               appearance-none cursor-pointer
                               focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary
                               hover:border-outline transition-colors">
                    <option value="" disabled selected>Selecciona una empresa...</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                    <span class="material-symbols-outlined text-on-surface-variant"
                        style="font-size:20px; font-variation-settings:'wght' 300">
                        expand_more
                    </span>
                </div>
            </div>
        </div>

        <button type="submit"
            class="w-full flex items-center justify-center gap-2 px-4 py-3
                       rounded-xl text-sm font-semibold text-on-primary bg-primary
                       hover:opacity-90 active:scale-[0.98] transition-all">
            {{ __('Continuar') }}
            <span class="material-symbols-outlined" style="font-size:18px; font-variation-settings:'wght' 400">
                arrow_forward
            </span>
        </button>
    </form>
</x-guest-layout>
