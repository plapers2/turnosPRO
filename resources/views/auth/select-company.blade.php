<x-guest-layout>
    <div class="max-w-md mx-auto mt-10">

        <h2 class="text-xl font-bold mb-4">
            Selecciona una empresa
        </h2>

        <form method="POST" action="{{ route('company.select.store') }}">
            @csrf

            <div class="mb-5">
                <x-form.select id="company_id" name="company_id" required>
                    <option value="">
                        Selecciona una empresa
                    </option>

                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">
                            {{ $company->name }}
                        </option>
                    @endforeach
                </x-form.select>
            </div>

            <x-primary-button>
                {{ __('Continuar') }}
            </x-primary-button>
        </form>

    </div>
</x-guest-layout>
