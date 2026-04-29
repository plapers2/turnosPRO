<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update') }} Cliente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Update') }} Cliente</h1>
                            <p class="mt-2 text-sm text-gray-700">Update existing {{ __('Cliente') }}.</p>
                        </div>
                        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                            <a type="button" href="{{ route('dashboard') }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Back</a>
                        </div>
                    </div>

                    <div class="flow-root">
                        <div class="mt-8 overflow-x-auto">
                            <div class="max-w-xl py-2 align-middle">
                                <form method="POST" action="{{ route('customer.profile.update') }}" role="form" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-6">
                                        <div>
                                            <x-input-label for="name" :value="__('Name')" />
                                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $cliente?->name)" />
                                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                        </div>

                                        <div>
                                            <x-input-label for="phone" :value="__('Phone')" />
                                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $cliente?->phone)" />
                                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                        </div>

                                        {{-- Separador contraseña --}}
                                        <hr>
                                        <p class="text-sm text-gray-500">Completa estos campos solo si quieres cambiar tu contraseña</p>

                                        <div>
                                            <x-input-label for="current_password" value="Contraseña actual" />
                                            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" />
                                            <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
                                        </div>

                                        <div>
                                            <x-input-label for="new_password" value="Nueva contraseña" />
                                            <x-text-input id="new_password" name="new_password" type="password" class="mt-1 block w-full" />
                                            <x-input-error class="mt-2" :messages="$errors->get('new_password')" />
                                        </div>

                                        <div>
                                            <x-input-label for="new_password_confirmation" value="Confirmar nueva contraseña" />
                                            <x-text-input id="new_password_confirmation" name="new_password_confirmation" type="password" class="mt-1 block w-full" />
                                            <x-input-error class="mt-2" :messages="$errors->get('new_password_confirmation')" />
                                        </div>

                                        <div class="flex items-center gap-4">
                                            <x-primary-button>Guardar cambios</x-primary-button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>