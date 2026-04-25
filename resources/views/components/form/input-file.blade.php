@props(['name', 'id' => null])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);
@endphp

<div
    class="max-w-lg relative border-2 border-dashed rounded-lg p-6 transition
    {{ $hasError ? 'border-red-400 bg-red-50/30' : 'border-gray-300' }}">

    <!-- INPUT REAL (ÚNICO) -->
    <input id="{{ $id }}" name="{{ $name }}" type="file"
        class="absolute inset-0 w-full h-full opacity-0 z-50 cursor-pointer" />

    <!-- UI -->
    <div class="text-center pointer-events-none">

        <img class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="upload">

        <h3 class="mt-2 text-sm font-medium text-gray-900">
            <span>Arrastra y suelta</span>
            <span class="text-indigo-600"> o sube</span>
            <span>el archivo</span>
        </h3>

        <p class="mt-1 text-xs text-gray-500">
            PNG, JPG, GIF hasta 10MB
        </p>
    </div>

    <!-- PREVIEW -->
    <img id="{{ $id }}_preview" src="{{ old($name, $service->image ?? '') }}"
        class="mt-4 mx-auto max-h-40 {{ old($name, $service->image ?? false) ? '' : 'hidden' }}">
</div>

<!-- ERROR -->
@error($name)
    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
        <span class="material-symbols-outlined text-[14px]">error</span>
        {{ $message }}
    </p>
@enderror
