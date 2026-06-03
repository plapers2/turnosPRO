<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nueva Cita
        </h2>
    </x-slot>
    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-2">
            <livewire:appointments.employee-appointment-create />
        </div>
    </div>
</x-app-layout>