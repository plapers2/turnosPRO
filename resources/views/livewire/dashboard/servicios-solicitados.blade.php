<div>
    <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/20 shadow-sm">
        <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
            <span class="material-symbols-rounded ms-outline" style="font-size:1.1rem;">star</span>
            Servicios más solicitados
        </h2>

        @if (empty($services))
            <p class="text-sm text-on-surface-variant">Sin datos para este período.</p>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($services as $s)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-rounded text-on-surface-variant ms-outline"
                                style="font-size:1rem">auto_fix_normal</span>
                            <span class="text-sm text-on-surface">{{ $s['name'] }}</span>
                        </div>
                        <span class="text-xs font-semibold bg-primary/10 text-primary px-2 py-0.5 rounded-full">
                            {{ $s['count'] }}x
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
