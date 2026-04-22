<?php

use Livewire\Component;
use App\Models\Service;

new class extends Component {
    public $services;

    public $isOpen = false;
    public $serviceId;
    public $nombre;
    public $descripcion;
    public $duracion;
    public $precio;
    public $imagen;
    public $empresa;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'duracion' => 'required|integer',
        'precio' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->loadServices();
    }

    public function loadServices()
    {
        $this->services = Service::latest()->get();
    }

    public function openModal()
    {
        $this->resetFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);

        $this->serviceId = $service->id;
        $this->nombre = $service->name;
        $this->descripcion = $service->description;
        $this->precio = $service->price;
        $this->duracion = $service->duration;
        $this->empresa = $service->company_id;

        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate();

        Service::updateOrCreate(
            ['id' => $this->serviceId],
            [
                'name' => $this->nombre,
                'description' => $this->descripcion,
                'duration' => $this->duracion,
                'price' => $this->precio,
                'company_id' => $this->empresa,
            ],
        );

        session()->flash('success', $this->serviceId ? 'Actualizado' : 'Creado');

        $this->closeModal();
        $this->loadServices();
    }

    public function delete($id)
    {
        Service::find($id)?->delete();
        $this->loadServices();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->reset(['serviceId', 'nombre', 'descripcion', 'duracion', 'precio', 'empresa']);
    }
};
?>

<div x-data="{ open: false }" x-on:open-modal.window="open = true" x-on:close-modal.window="open = false"
    @keydown.escape.window="open = false">
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">
        <!-- TopAppBar -->
        <header
            class="bg-[#fcf9f3]/80 backdrop-blur-md text-primary font-['Inter'] text-2xl font-bold tracking-tight h-20 flex items-center flex-col lg:flex-row justify-between px-8 w-full mt-10 mb-2">
            <h2 class="text-2xl font-bold tracking-tight -ml-2">Gestión de Servicios</h2>
            <button @click="$dispatch('open-modal')"
                class="bg-primary-container mt-2 w-full lg:w-auto text-center justify-center hover:bg-primary/90 text-white scale-98 transition-transform px-6 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2">
                + Nuevo servicio
            </button>
        </header>
        <!-- Canvas -->
        <div class="p-8 pb-20">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service Card -->
                @foreach ($services as $service)
                    <article
                        class="bg-surface-container-lowest rounded-xl flex flex-col shadow-[0_10px_30px_rgba(95,94,90,0.06)] transition-all hover:-translate-y-1 duration-300">
                        <figure class="w-full h-48 overflow-hidden rounded-t-xl">
                            <img alt="Tratamiento Facial" class="w-full h-full object-cover"
                                src='{{ $service->image }}' />
                        </figure>
                        <div class="p-6 flex flex-col gap-6 flex-1">
                            <div>
                                <h3 class="text-xl font-bold text-primary mb-2 font-headline tracking-tight">
                                    {{ $service->name }}</h3>
                                <p class="text-on-surface-variant text-sm leading-relaxed line-clamp-2">
                                    {{ $service->description }}</p>
                            </div>
                            <div class="flex flex-wrap gap-3 mt-auto">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-primary-fixed text-on-primary-fixed text-xs font-semibold font-label">
                                    <span class="material-symbols-outlined text-[16px]"
                                        data-icon="schedule">schedule</span>
                                    {{ $service->duration }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-secondary-fixed text-on-secondary-fixed text-xs font-semibold font-label">
                                    <span class="material-symbols-outlined text-[16px]"
                                        data-icon="payments">payments</span>
                                    {{ $service->price }}
                                </span>
                            </div>
                            <div class="flex justify-end gap-4 pt-4 mt-2">
                                <button
                                    class="text-sm font-semibold text-primary hover:text-primary-container transition-colors px-2 py-1 rounded"
                                    wire:click="edit({{ $service->id }})">Editar</button>
                                <button
                                    class="text-sm font-semibold text-error hover:text-on-error-container transition-colors px-2 py-1 rounded"
                                    wire:click='delete({{ $service->id }})'>Eliminar</button>
                            </div>
                        </div>
                    </article>
                @endforeach

            </div>
        </div>
    </main>

    <div class="fixed inset-0 flex justify-end z-[70] transition-all duration-300"
        :class="open ? 'pointer-events-auto' : 'pointer-events-none'">
        <div class="absolute inset-0 transition-all duration-300"
            :class="open
                ?
                'bg-black/40 backdrop-blur-md opacity-100' :
                'bg-black/0 backdrop-blur-0 opacity-0'"
            @click="$dispatch('close-modal')">
        </div>
        <!-- Drawer Panel -->
        <div class="relative w-full max-w-md h-full
    bg-surface-container-lowest
    shadow-[-20px_0_60px_rgba(28,28,25,0.08)]
    border-l border-surface-variant/20
    flex flex-col
    transform transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
            :class="open
                ?
                'translate-x-0 opacity-100' :
                'translate-x-full opacity-0'">
            <!-- Drawer Header -->
            <div class="px-8 py-8 flex items-center justify-between">
                <h2 class="font-headline text-2xl font-bold text-on-surface tracking-tight">Nuevo Servicio</h2>
                <button @click="$dispatch('close-modal')"
                    class="text-on-surface-variant hover:bg-surface-container p-2 rounded-full transition-colors flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Drawer Body (Form) -->
            <div class="flex-1 overflow-y-auto px-8 pb-8">
                <form class="space-y-8">
                    <!-- Input: Nombre -->
                    <div>
                        <label class="block font-label text-sm font-semibold text-on-surface mb-2" for="nombre">Nombre
                            del servicio</label>
                        <input
                            class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-lg px-4 py-3.5 font-body text-base text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:bg-surface-bright focus:border-primary/30 focus:ring-4 focus:ring-primary/5 transition-all shadow-sm"
                            id="nombre" placeholder="Ej. Manicura Spa" type="text" />
                    </div>
                    <!-- Textarea: Descripción -->
                    <div>
                        <label class="block font-label text-sm font-semibold text-on-surface mb-2"
                            for="descripcion">Descripción</label>
                        <textarea
                            class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-lg px-4 py-3.5 font-body text-base text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:bg-surface-bright focus:border-primary/30 focus:ring-4 focus:ring-primary/5 transition-all shadow-sm resize-none"
                            id="descripcion" placeholder="Detalla los beneficios y pasos del servicio..." rows="4"></textarea>
                    </div>
                    <!-- Grid for minor fields -->
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Select: Duración -->
                        <div>
                            <label class="block font-label text-sm font-semibold text-on-surface mb-2"
                                for="duracion">Duración</label>
                            <div class="relative">
                                <select
                                    class="appearance-none w-full bg-surface-container-lowest border border-outline-variant/30 rounded-lg pl-4 pr-10 py-3.5 font-body text-base text-on-surface focus:outline-none focus:bg-surface-bright focus:border-primary/30 focus:ring-4 focus:ring-primary/5 transition-all shadow-sm cursor-pointer"
                                    id="duracion">
                                    <option value="15">15 min</option>
                                    <option value="30">30 min</option>
                                    <option selected="" value="45">45 min</option>
                                    <option value="60">60 min</option>
                                    <option value="90">90 min</option>
                                </select>
                                <span
                                    class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <!-- Input: Precio -->
                        <div>
                            <label class="block font-label text-sm font-semibold text-on-surface mb-2"
                                for="precio">Precio ref. (COP)</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-body font-medium">$</span>
                                <input
                                    class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-lg pl-8 pr-4 py-3.5 font-body text-base text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:bg-surface-bright focus:border-primary/30 focus:ring-4 focus:ring-primary/5 transition-all shadow-sm"
                                    id="precio" placeholder="0" type="text" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Drawer Footer -->
            <div class="px-8 py-6 bg-surface-container-lowest border-t border-surface-variant/20 flex gap-4 mt-auto">
                <button @click="$dispatch('close-modal')"
                    class="flex-1 bg-surface-container text-on-surface px-6 py-3.5 rounded-lg font-label text-sm font-semibold hover:bg-surface-container-high transition-colors">
                    Cancelar
                </button>
                <button
                    class="flex-[2] bg-primary-container text-on-primary-fixed px-6 py-3.5 rounded-lg font-label text-sm font-semibold shadow-[0_8px_20px_rgba(102,58,0,0.12)] hover:bg-[#965a0d] hover:shadow-[0_12px_24px_rgba(102,58,0,0.18)] transition-all active:scale-[0.98]">
                    Guardar servicio
                </button>
            </div>
        </div>
    </div>
</div>
