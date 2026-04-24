@props(['id'])
<div class="max-w-lg relative border-2 border-gray-300 border-dashed rounded-lg p-6">
    <input id='{{ $id }}'' name='{{ $id }}' type="file"
        class="absolute inset-0 w-full h-full opacity-0 z-50" />
    <div class="text-center">
        <img class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="">

        <h3 class="mt-2 text-sm font-medium text-gray-900">
            <label for="file-upload" class="relative cursor-pointer">
                <span>Arrastra y suelta</span>
                <span class="text-indigo-600"> o sube</span>
                <span>el archivo</span>
                <input id="file-upload" name="file-upload" type="file" class="sr-only">
            </label>
        </h3>
        <p class="mt-1 text-xs text-gray-500">
            PNG, JPG, GIF up to 10MB
        </p>
    </div>

    <img src="" class="mt-4 mx-auto max-h-40 hidden" id="preview">
</div>
