         <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">

             <!-- FORM -->
             <div class="lg:col-span-2 space-y-8">

                 <!-- CARD PRINCIPAL -->
                 <div
                     class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-8">

                     <div>
                         <h2 class="text-lg font-semibold text-primary mb-1">
                             Información básica
                         </h2>
                         <p class="text-sm text-on-surface-variant">
                             Define los detalles principales del servicio
                         </p>
                     </div>

                     <x-form.field label="Nombre del servicio" for="name">
                         <x-form.input id="name" name="nombre" :value="old('nombre', $service?->name)" type="text"
                             placeholder="Ej. Manicura Spa" class="focus:ring-primary/10 focus:border-primary/40" />

                     </x-form.field>

                     <x-form.field label="Descripción" for="description">
                         <x-form.textarea texto="{{ old('descripcion', $service->description) }}" name="descripcion"
                             id="description">
                         </x-form.textarea>
                     </x-form.field>

                 </div>

                 <!-- CARD DETALLES -->
                 <div
                     class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

                     <h2 class="text-lg font-semibold text-primary">
                         Configuración
                     </h2>

                     <div class="grid grid-cols-2 gap-6">

                         <x-form.field label="Duración (min)" for="duration">
                             <x-form.input name="duracion" :value="old('duracion', $service->duration ?? '')" type="number" id="duration"
                                 class="focus:ring-secondary/10 focus:border-secondary/40" placeholder="2 (minutos)" />
                         </x-form.field>

                         <x-form.field label="Precio (COP)" for="price">
                             <x-form.input name="precio" :value="old('precio', $service->price ?? '')" type="number" id="price"
                                 class="focus:ring-secondary/10 focus:border-secondary/40" placeholder="Ej: $20.000" />
                         </x-form.field>

                     </div>

                 </div>

                 <!-- BOTONES -->
                 <div class="flex justify-end gap-4 pt-4">

                     <a href="{{ route('services.index') }}"
                         class="px-5 py-2.5 rounded-lg text-sm font-semibold bg-surface-container hover:bg-surface-container-high transition">
                         Cancelar
                     </a>

                     <button type="submit"
                         class="px-6 py-2.5 rounded-lg text-sm font-semibold bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
                         Guardar servicio
                     </button>

                 </div>

             </div>

             <!-- SIDEBAR -->
             <div class="space-y-8">

                 <div class="bg-surface-container rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">

                     <h3 class="text-md font-semibold text-primary">
                         Imagen del servicio
                     </h3>

                     <x-form.input-file name="imagen" id="image" />

                     <img id="preview" src="{{ $service->image ? asset('storage/' . $service->image) : '' }}"
                         class="{{ isset($service) ? '' : 'hidden' }} w-full h-40 object-cover rounded-lg border border-outline-variant/20">
                 </div>

                 <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">
                     <h3 class="text-sm font-semibold text-primary">
                         Recomendaciones
                     </h3>

                     <ul class="text-sm text-on-surface-variant space-y-2">
                         <li>• Usa imágenes reales del servicio</li>
                         <li>• Evita descripciones genéricas</li>
                         <li>• Ajusta el precio al mercado local</li>
                     </ul>
                 </div>

                 <div class="bg-tertiary-container/10 rounded-xl p-6 border border-tertiary/10">
                     <p class="text-sm text-tertiary font-medium">
                         Este servicio será visible inmediatamente después de guardarlo
                     </p>
                 </div>

                 <input type="hidden" value="1" id="company_id" name="company_id">
             </div>

         </div>

         <script>
             const input = document.getElementById('image');
             const preview = document.getElementById('preview');
             const placeholder = document.getElementById('placeholder');

             input.addEventListener('change', e => {
                 const file = e.target.files[0];
                 if (!file) return;

                 const reader = new FileReader();
                 reader.onload = () => {
                     preview.src = reader.result;
                     preview.classList.remove('hidden');
                     placeholder.classList.add('hidden');
                 };
                 reader.readAsDataURL(file);
             });
         </script>
