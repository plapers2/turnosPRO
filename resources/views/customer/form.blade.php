<div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- FORM -->
    <div class="lg:col-span-2 space-y-8">

        <!-- CARD INFORMACIÓN BÁSICA -->
        <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-8">

            <div>
                <h2 class="text-lg font-semibold text-primary mb-1">Información básica</h2>
                <p class="text-sm text-on-surface-variant">Define los detalles principales de la empresa</p>
            </div>

            <x-form.field label="Nombre" for="name">
                <x-form.input id="name" name="name" type="text"
                    :value="old('name', $customer?->name)"
                    placeholder="Ej. Salón Bella Vista"
                    class="focus:ring-primary/10 focus:border-primary/40" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </x-form.field>

            <x-form.field label="Teléfono" for="phone">
                <x-form.input id="phone" name="phone" type="text"
                    :value="old('phone', $customer?->phone)"
                    placeholder="Ej. 3001234567"
                    class="focus:ring-secondary/10 focus:border-secondary/40" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </x-form.field>

            <x-form.field label="Correo electrónico" for="email">
                <x-form.input id="email" name="email" type="email"
                    :value="old('email', $customer?->email)"
                    placeholder="Ej. contacto@empresa.com"
                    class="focus:ring-primary/10 focus:border-primary/40" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </x-form.field>

        </div>

        <!-- BOTONES -->
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('companies.index') }}"
                class="px-5 py-2.5 rounded-lg text-sm font-semibold bg-surface-container hover:bg-surface-container-high transition">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-2.5 rounded-lg text-sm font-semibold bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
                Guardar Cliente
            </button>
        </div>

    </div>

</div>