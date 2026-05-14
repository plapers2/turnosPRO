<div>
    <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/20 shadow-sm">
        <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
            <span class="material-symbols-rounded ms-outline" style="font-size:1.1rem;">upcoming</span>
            Próximas citas
        </h2>

        @if (empty($appointments))
            <p class="text-sm text-on-surface-variant flex items-center gap-2">
                <span class="material-symbols-rounded ms-outline" style="font-size:1.1rem">event_busy</span>
                No hay citas próximas.
            </p>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($appointments as $a)
                    @php
                        $statusCfg = [
                            'confirmed' => [
                                'label' => 'Confirmada',
                                'icon' => 'check_circle',
                                'bg' => '#f0fdf4',
                                'color' => '#16a34a',
                            ],
                            'pending' => [
                                'label' => 'Pendiente',
                                'icon' => 'hourglass_empty',
                                'bg' => '#fffbeb',
                                'color' => '#d97706',
                            ],
                            'completed' => [
                                'label' => 'Completada',
                                'icon' => 'verified',
                                'bg' => '#eff6ff',
                                'color' => '#2563eb',
                            ],
                            'cancelled' => [
                                'label' => 'Cancelada',
                                'icon' => 'cancel',
                                'bg' => '#fff1f2',
                                'color' => '#dc2626',
                            ],
                        ];
                        $cfg = $statusCfg[$a['status']] ?? [
                            'label' => $a['status'],
                            'icon' => 'info',
                            'bg' => '#f5f5f5',
                            'color' => '#555',
                        ];
                    @endphp
                    <div class="flex items-center justify-between p-4 rounded-xl bg-surface-container-low">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-primary ms-outline mt-0.5"
                                style="font-size:1.1rem">schedule</span>
                            <div>
                                <p class="text-sm font-semibold text-on-surface">
                                    <span class="text-primary">{{ $a['time'] }}</span>
                                    &nbsp;·&nbsp;{{ $a['name'] }}
                                </p>
                                <p class="text-xs text-on-surface-variant mt-0.5">
                                    {{ $a['service'] }}{{ $a['staff'] !== 'Sin asignar' ? ' — ' . $a['staff'] : '' }}
                                </p>
                            </div>
                        </div>
                        <span class="status-badge" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }}">
                            <span class="material-symbols-rounded" style="font-size:.85rem">{{ $cfg['icon'] }}</span>
                            {{ $cfg['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
