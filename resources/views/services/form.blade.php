<div class="space-y-6">

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $service?->name)"
            autocomplete="name" placeholder="Name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div>
        <x-input-label for="description" :value="__('Description')" />
        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description', $service?->description)"
            autocomplete="description" placeholder="Description" />
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>
    <div>
        <x-input-label for="duration" :value="__('Duration')" />
        <x-text-input id="duration" name="duration" type="text" class="mt-1 block w-full" :value="old('duration', $service?->duration)"
            autocomplete="duration" placeholder="Duration" />
        <x-input-error class="mt-2" :messages="$errors->get('duration')" />
    </div>
    <div>
        <x-input-label for="price" :value="__('Price')" />
        <x-text-input id="price" name="price" type="text" class="mt-1 block w-full" :value="old('price', $service?->price)"
            autocomplete="price" placeholder="Price" />
        <x-input-error class="mt-2" :messages="$errors->get('price')" />
    </div>
    <div>
        <x-input-label for="image" :value="__('Image')" />
        <x-text-input id="image" name="image" type="text" class="mt-1 block w-full" :value="old('image', $service?->image)"
            autocomplete="image" placeholder="Image" />
        <x-input-error class="mt-2" :messages="$errors->get('image')" />
    </div>
    <div>
        <x-input-label for="state" :value="__('State')" />
        <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state', $service?->state)"
            autocomplete="state" placeholder="State" />
        <x-input-error class="mt-2" :messages="$errors->get('state')" />
    </div>
    <div>
        <x-input-label for="state" :value="__('Company')" />
        <select class="mt-1 block w-full border-gray-300 rounded-md py-2     px-2 text-gray-800 bg-white shadow"
            name="company_id" id="company_id">
            @forelse ($companies as $company)
                <option name="company_id" value="{{ $company->id }}">
                    hola {{ $company->name }}
                </option>
            @empty
                <option value="">No hay ninguna empresa creada</option>
            @endforelse ()


            <x-input-error class="mt-2" :messages="$errors->get('company_id')" />
        </select>

    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>
