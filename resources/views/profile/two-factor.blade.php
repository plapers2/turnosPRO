<x-app-layout>
    <main class="flex-1 flex flex-col min-h-0 overflow-y-auto bg-surface">

        <!-- HEADER -->
        <header
            class="relative bg-[#fcf9f3]/80 backdrop-blur-md border border-outline-variant/20
            rounded-xl mx-8 mt-10 mb-4 px-6 py-5 flex flex-col lg:flex-row
            items-start lg:items-center justify-between gap-4 shadow-[0_8px_20px_rgba(95,94,90,0.04)]">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">shield_lock</span>
                    </div>
                    <h2 class="text-xl font-bold text-primary tracking-tight">
                        Autenticación de dos factores
                    </h2>
                </div>
                <p class="text-sm text-on-surface-variant ml-13">
                    Protege tu cuenta con una capa extra de seguridad
                </p>
            </div>
            <a href="{{ route('profile.settings') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-on-surface-variant
                hover:text-primary transition-colors px-3 py-2 rounded-lg hover:bg-primary/5">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Volver al perfil
            </a>
        </header>

        <!-- CANVAS -->
        <div class="px-8 pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- COLUMNA PRINCIPAL -->
                <div class="lg:col-span-2 space-y-6">

                    {{--
                        CORRECCIÓN: Fortify puede flashear errores en el bag 'default' o en bags
                        específicos. Revisamos ambos para no perder ningún mensaje de error.
                        También añadimos $errors->getBag('default') como fallback explícito.
                    --}}
                    @if ($errors->any() || $errors->getBag('default')->any())
                        <div class="flex items-start gap-3 px-5 py-4 rounded-xl bg-red-50 border border-red-200">
                            <span class="material-symbols-outlined text-red-500 text-[20px] mt-0.5 flex-shrink-0"
                                style="font-variation-settings:'FILL' 1">error</span>
                            <div>
                                <p class="text-sm font-semibold text-red-800 mb-1">Corrige los siguientes errores:</p>
                                <ul class="space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-xs text-red-700 flex items-start gap-1.5">
                                            <span class="mt-0.5 flex-shrink-0">·</span>
                                            {{ $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif


                    @if (session('success'))
                        <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-green-50 border border-green-200">
                            <span class="material-symbols-outlined text-green-500 text-[22px]"
                                style="font-variation-settings:'FILL' 1">check_circle</span>
                            <p class="text-sm text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-red-50 border border-red-200">
                            <span class="material-symbols-outlined text-red-500 text-[22px]">warning</span>
                            <p class="text-sm text-red-800">{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- ESTADO: 2FA ACTIVO -->
                    @if (auth()->user()->two_factor_confirmed_at)

                        <!-- Banner activo -->
                        <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-green-50 border border-green-200">
                            <span class="material-symbols-outlined text-green-500 text-[22px]"
                                style="font-variation-settings:'FILL' 1">verified_user</span>
                            <div>
                                <p class="text-sm font-semibold text-green-800">2FA activado</p>
                                <p class="text-xs text-green-700">Tu cuenta está protegida con autenticación de dos
                                    factores.</p>
                            </div>
                        </div>

                        <!-- Códigos de recuperación -->
                        @if ($recoveryCodes)
                            <div
                                class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-5">
                                <div>
                                    <h2 class="text-lg font-semibold text-primary mb-1">Códigos de recuperación</h2>
                                    <p class="text-sm text-on-surface-variant">
                                        Guárdalos en un lugar seguro. Cada código solo puede usarse una vez si pierdes
                                        acceso a tu autenticador.
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    @foreach ($recoveryCodes as $code)
                                        <div
                                            class="flex items-center justify-between px-4 py-2.5 rounded-lg bg-surface-container border border-outline-variant/20">
                                            <code
                                                class="text-sm font-mono text-on-surface tracking-wider">{{ $code }}</code>
                                            <button type="button" onclick="copyCode('{{ $code }}', this)"
                                                class="text-on-surface-variant hover:text-primary transition ml-2">
                                                <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <form method="POST" action="/user/two-factor-recovery-codes">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold
                                        bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition">
                                        <span class="material-symbols-outlined text-[16px]">autorenew</span>
                                        Regenerar códigos
                                    </button>
                                </form>
                            </div>
                        @endif

                        <!-- Desactivar 2FA -->
                        <div
                            class="bg-surface-container-lowest rounded-xl p-8 border border-red-100 shadow-sm space-y-4">
                            <div>
                                <h2 class="text-lg font-semibold text-red-600 mb-1">Zona de peligro</h2>
                                <p class="text-sm text-on-surface-variant">
                                    Al desactivar el 2FA, tu cuenta quedará protegida solo con tu contraseña.
                                </p>
                            </div>
                            <form method="POST" action="/user/two-factor-authentication" id="form-disable-2fa">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDisable2FA()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold
                                    bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition">
                                    <span class="material-symbols-outlined text-[16px]">no_encryption</span>
                                    Desactivar 2FA
                                </button>
                            </form>
                        </div>

                        <!-- ESTADO: QR generado, esperando confirmación -->
                    @elseif(auth()->user()->two_factor_secret)
                        <!-- Banner pendiente -->
                        <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-amber-50 border border-amber-200">
                            <span class="material-symbols-outlined text-amber-500 text-[22px]">pending</span>
                            <div>
                                <p class="text-sm font-semibold text-amber-800">Configuración pendiente</p>
                                <p class="text-xs text-amber-700">Escanea el QR y confirma con un código para activar el
                                    2FA.</p>
                            </div>
                        </div>

                        <div
                            class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-primary mb-1">Escanea el código QR</h2>
                                <p class="text-sm text-on-surface-variant">
                                    Abre Google Authenticator, Authy u otra aplicación TOTP y escanea el código.
                                </p>
                            </div>

                            <!-- QR -->
                            <div class="flex justify-center">
                                <div
                                    class="p-4 rounded-xl bg-white border border-outline-variant/20 shadow-sm inline-block">
                                    {!! $qrSvg !!}
                                </div>
                            </div>

                            <!-- Clave manual -->
                            <div class="flex flex-col gap-1.5">
                                <p class="text-xs font-medium text-on-surface-variant">¿No puedes escanear? Ingresa esta
                                    clave manualmente:</p>
                                <div
                                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-surface-container border border-outline-variant/20">
                                    <code
                                        class="flex-1 text-sm font-mono tracking-widest text-on-surface">{{ $secret }}</code>
                                    <button type="button" onclick="copyCode('{{ $secret }}', this)"
                                        class="text-on-surface-variant hover:text-primary transition">
                                        <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirmar código OTP -->
                            {{--
                                CORRECCIÓN PRINCIPAL: El form ya no usa submitOtp() para rellenar el hidden
                                antes del submit. En su lugar, el campo `code` se construye directamente
                                con name en cada input y se ensambla en el hidden on-the-fly.
                                Además, si hay un error de validación, los boxes se repintan con el
                                valor old('code') para que el usuario vea qué envió.
                            --}}
                            <form method="POST" action="/user/confirmed-two-factor-authentication" class="space-y-4"
                                id="form-confirm-otp">
                                @csrf

                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-medium text-on-surface">Código de confirmación</label>

                                    <div class="flex gap-2" id="otp-boxes">
                                        @php
                                            // Si hay un error, rellenamos los boxes con el valor anterior
                                            $oldCode = old('code', '');
                                        @endphp

                                        @for ($i = 0; $i < 6; $i++)
                                            <input type="text" inputmode="numeric" maxlength="1"
                                                id="otp-{{ $i }}" value="{{ $oldCode[$i] ?? '' }}"
                                                autocomplete="off"
                                                class="w-11 h-14 text-center text-xl font-semibold rounded-xl border
                                                       border-outline-variant/30 focus:border-primary
                                                       focus:ring-2 focus:ring-primary/10 outline-none
                                                       bg-surface text-on-surface transition-all
                                                       {{ $errors->has('code') ? 'border-red-400 bg-red-50' : '' }}" />
                                        @endfor

                                        {{--
                                            CORRECCIÓN: El hidden recibe el value con old('code')
                                            para que en el re-submit tras error se envíe el código correcto.
                                        --}}
                                        <input type="hidden" name="code" id="otp-hidden"
                                            value="{{ old('code', '') }}" />
                                    </div>

                                    {{--
                                        CORRECCIÓN: Mostramos el error de 'code' que envía Fortify.
                                        Fortify usa la clave 'code' en el MessageBag por defecto.
                                    --}}
                                    @error('code')
                                        <div
                                            class="flex items-center gap-2 px-3 py-2 rounded-lg bg-red-50 border border-red-200">
                                            <span
                                                class="material-symbols-outlined text-red-500 text-[16px] flex-shrink-0">error</span>
                                            <p class="text-xs text-red-700">{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>

                                <button type="button" onclick="submitOtp()"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold
                                    bg-primary text-white hover:bg-primary/90 transition shadow-md">
                                    <span class="material-symbols-outlined text-[16px]">verified_user</span>
                                    Confirmar y activar
                                </button>
                            </form>
                        </div>

                        <!-- ESTADO: 2FA INACTIVO -->
                    @else
                        <!-- Banner inactivo -->
                        <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-red-50 border border-red-200">
                            <span class="material-symbols-outlined text-red-400 text-[22px]">gpp_bad</span>
                            <div>
                                <p class="text-sm font-semibold text-red-800">2FA desactivado</p>
                                <p class="text-xs text-red-700">Tu cuenta solo está protegida con contraseña.</p>
                            </div>
                        </div>

                        <div
                            class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-primary mb-1">Activar autenticación de dos
                                    factores</h2>
                                <p class="text-sm text-on-surface-variant">
                                    Al activarlo, necesitarás un código de tu aplicación autenticadora cada vez que
                                    inicies sesión.
                                </p>
                            </div>

                            <!-- Beneficios -->
                            <div class="space-y-3">
                                @foreach ([['security', 'Protección extra si tu contraseña es comprometida'], ['smartphone', 'Compatible con Google Authenticator, Authy y más'], ['key', 'Códigos de recuperación de emergencia incluidos']] as [$icon, $text])
                                    <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                        <span
                                            class="material-symbols-outlined text-primary text-[18px]">{{ $icon }}</span>
                                        {{ $text }}
                                    </div>
                                @endforeach
                            </div>

                            <form method="POST" action="/user/two-factor-authentication">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold
                                    bg-primary text-white hover:bg-primary/90 transition shadow-md">
                                    <span class="material-symbols-outlined text-[16px]">shield_lock</span>
                                    Activar 2FA
                                </button>
                            </form>
                        </div>

                    @endif

                </div>

                <!-- SIDEBAR -->
                <div class="space-y-6">

                    <!-- Info card -->
                    <div
                        class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[18px]">info</span>
                            <h3 class="text-sm font-semibold text-primary">¿Cómo funciona?</h3>
                        </div>
                        <ol class="space-y-3">
                            @foreach (['Activa el 2FA desde esta página.', 'Escanea el QR con tu aplicación autenticadora.', 'Confirma con el código de 6 dígitos.', 'En cada login, ingresarás tu contraseña + el código.'] as $i => $step)
                                <li class="flex items-start gap-3 text-xs text-on-surface-variant">
                                    <span
                                        class="w-5 h-5 rounded-full bg-primary/10 text-primary text-[11px] font-bold
                                                 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        {{ $i + 1 }}
                                    </span>
                                    {{ $step }}
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <!-- Apps recomendadas -->
                    <div
                        class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-3">
                        <h3 class="text-sm font-semibold text-primary">Apps recomendadas</h3>
                        @foreach ([['Google Authenticator', 'Android e iOS'], ['Authy', 'Android, iOS y escritorio'], ['Microsoft Authenticator', 'Android e iOS']] as [$app, $plat])
                            <div class="flex items-center gap-3">
                                <span
                                    class="material-symbols-outlined text-on-surface-variant text-[18px]">smartphone</span>
                                <div>
                                    <p class="text-xs font-semibold text-on-surface">{{ $app }}</p>
                                    <p class="text-[11px] text-on-surface-variant">{{ $plat }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Volver al perfil -->
                    <a href="{{ route('profile.settings') }}"
                        class="w-full flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold
                        bg-surface-container hover:bg-surface-container-high transition text-on-surface">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        Volver al perfil
                    </a>

                </div>
            </div>
        </div>

    </main>
</x-app-layout>

<script>
    const boxes = Array.from({
        length: 6
    }, (_, i) => document.getElementById('otp-' + i)).filter(Boolean);
    const hidden = document.getElementById('otp-hidden');

    function syncHidden() {
        if (hidden) hidden.value = boxes.map(b => b.value).join('');
    }

    // Sincronizar desde el principio (por si old('code') pre-rellenó los boxes)
    syncHidden();

    boxes.forEach((box, i) => {
        box.addEventListener('input', e => {
            const val = e.target.value.replace(/\D/g, '');
            box.value = val ? val[0] : ''; // solo 1 dígito por box
            syncHidden();
            if (val && i < 5) boxes[i + 1].focus();
        });

        box.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !box.value && i > 0) {
                boxes[i - 1].value = '';
                boxes[i - 1].focus();
                syncHidden();
            }
        });

        box.addEventListener('paste', e => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/\D/g, '').slice(0, 6);
            paste.split('').forEach((ch, idx) => {
                if (boxes[idx]) boxes[idx].value = ch;
            });
            syncHidden();
            boxes[Math.min(paste.length - 1, 5)]?.focus();
        });
    });

    // Auto-foco en el primer box vacío (o el último si ya están todos llenos)
    if (boxes.length) {
        const firstEmpty = boxes.find(b => !b.value);
        (firstEmpty ?? boxes[boxes.length - 1]).focus();
    }

    // Copiar al portapapeles
    function copyCode(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const icon = btn.querySelector('.material-symbols-outlined');
            icon.textContent = 'check';
            icon.style.color = 'var(--color-primary, #5c6bc0)';
            setTimeout(() => {
                icon.textContent = 'content_copy';
                icon.style.color = '';
            }, 1500);
        });
    }

    // SweetAlert desactivar 2FA
    function confirmDisable2FA() {
        Swal.fire({
            title: '¿Desactivar el 2FA?',
            text: 'Tu cuenta quedará protegida solo con tu contraseña. Podrás activarlo nuevamente cuando quieras.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-disable-2fa').submit();
            }
        });
    }

    function submitOtp() {
        syncHidden(); // asegurar que el hidden tenga el valor actual

        const code = hidden.value;

        if (code.length < 6 || !/^\d{6}$/.test(code)) {
            Swal.fire({
                icon: 'warning',
                title: code.length < 6 ? 'Código incompleto' : 'Código inválido',
                text: code.length < 6 ?
                    'Debes ingresar los 6 dígitos del código de tu aplicación autenticadora.' :
                    'El código debe contener solo números.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#dc2626',
            }).then(() => {
                const firstEmpty = boxes.find(b => !b.value);
                if (firstEmpty) firstEmpty.focus();
            });
            return;
        }

        document.getElementById('form-confirm-otp').submit();
    }
</script>
