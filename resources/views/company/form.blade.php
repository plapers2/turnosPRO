<div class="space-y-6">
    
    <div>
        <x-input-label for="name" :value="__('Name')"/>
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $company?->name)" autocomplete="name" placeholder="Name"/>
        <x-input-error class="mt-2" :messages="$errors->get('name')"/>
    </div>
    <div>
        <x-input-label for="logo" :value="__('Logo')"/>
        <x-text-input id="logo" name="logo" type="text" class="mt-1 block w-full" :value="old('logo', $company?->logo)" autocomplete="logo" placeholder="Logo"/>
        <x-input-error class="mt-2" :messages="$errors->get('logo')"/>
    </div>
    <div>
        <x-input-label for="email" :value="__('Email')"/>
        <x-text-input id="email" name="email" type="text" class="mt-1 block w-full" :value="old('email', $company?->email)" autocomplete="email" placeholder="Email"/>
        <x-input-error class="mt-2" :messages="$errors->get('email')"/>
    </div>
    <div>
        <x-input-label for="address" :value="__('Address')"/>
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $company?->address)" autocomplete="address" placeholder="Address"/>
        <x-input-error class="mt-2" :messages="$errors->get('address')"/>
    </div>
    <div>
        <x-input-label for="phone" :value="__('Phone')"/>
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $company?->phone)" autocomplete="phone" placeholder="Phone"/>
        <x-input-error class="mt-2" :messages="$errors->get('phone')"/>
    </div>
    <div>
        <x-input-label for="state" :value="__('State')"/>
        <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state', $company?->state)" autocomplete="state" placeholder="State"/>
        <x-input-error class="mt-2" :messages="$errors->get('state')"/>
    </div>
    <div>
        <x-input-label for="type_company_id" :value="__('Type Company Id')"/>
        <x-text-input id="type_company_id" name="type_company_id" type="text" class="mt-1 block w-full" :value="old('type_company_id', $company?->type_company_id)" autocomplete="type_company_id" placeholder="Type Company Id"/>
        <x-input-error class="mt-2" :messages="$errors->get('type_company_id')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>