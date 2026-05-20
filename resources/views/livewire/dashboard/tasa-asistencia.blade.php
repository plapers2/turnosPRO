<div>
    <div class="bg-primary-container/10 rounded-2xl p-6 border border-primary/10 space-y-3">
        <p class="text-xs uppercase tracking-widest text-primary flex items-center gap-1.5">
            <span class="material-symbols-rounded" style="font-size:1rem;">monitoring</span>
            Tasa de citas completadas
        </p>
        <div class="text-5xl font-extrabold text-primary">
            {{ $asistencia['pct'] }}%
        </div>
        <p class="text-sm text-on-surface-variant">{{ $asistencia['text'] }}</p>
        <div class="h-1.5 bg-black/10 rounded-full overflow-hidden">
            <div class="h-full bg-primary transition-all duration-500" style="width: {{ $asistencia['pct'] }}%"></div>
        </div>
    </div>
</div>
