{{-- resources/views/auth/reset-link-invalid.blade.php --}}
<x-guest-layout>
    <div class="text-center py-10 max-w-md mx-auto">
        <h1 class="text-xl font-semibold text-gray-800">Enlace expirado</h1>
        <p class="mt-2 text-gray-600">{{ $message }}</p>
        <a href="{{ route('password.request') }}" class="mt-4 inline-block text-indigo-600 underline">
            Solicitar nuevo enlace
        </a>
    </div>
</x-guest-layout>
