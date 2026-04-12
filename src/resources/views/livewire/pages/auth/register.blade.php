<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $phone_number = '';
    public bool $rgpd_consent = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate(
            [
                'name'          => ['required', 'string', 'max:50'],
                'email'         => ['required', 'string', 'email', 'max:100', 'unique:' . User::class],
                'password'      => ['required', 'string', 'confirmed', Rules\Password::defaults()],
                'phone_number'  => ['required', 'numeric', 'digits:9'],
                'rgpd_consent'  => ['accepted'],
            ],
            // MENSAJES DE ERROR CUSTOM
            [
                // NOMBRE
                'name.required'             => 'El nombre completo debe venir relleno.',
                // TELÉFONO
                'phone_number.required'     => 'El Teléfono es obligatorio para avisarte si hay cambios en la pista.',
                'phone_number.numeric'      => 'El Teléfono solo admite números.',
                'phone_number.digits'       => 'El Teléfono debe tener 9 caracteres exactos',
                // EMAIL
                'email.unique'              => 'Este Correo Electrónico ya está registrado en el club.',
                // LEY PROTECCIÓN DE DATOS
                'rgpd_consent.accepted'     => 'Debes aceptar la ley de protección de datos para poder continuar.',
                // CONTRASEÑA
                'password.confirmed'        => 'Las contraseñas deben coincidir.',
                'password.min'              => 'La contraseña debe tener almenos 8 caracteres.',
                'password.required'         => 'La contraseña es obligatoria.',
            ],
        );

        // ASIGNO POR DEFECTO EL ROL DE PLAYER
        $playerRole = Role::where('name', 'player')->first();

        $user = User::create([
            'name'         => $this->name,
            'email'        => $this->email,
            'password'     => Hash::make($this->password),
            'phone_number' => $this->phone_number,
            'rgpd_consent' => $this->rgpd_consent,
            'role_id'      => $playerRole->id,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // GUARDO EL AVISO DE CUENTA CREADA EN LA SESIÓN
        session()->flash('swal', [
            'icon'  => 'success',
            'title' => '¡Bienvenido al Club, ' . $user->name . '!',
            'text'  => 'Ya puedes empezar a reservar tus pistas.',
        ]);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
};
?>

<div>
    <form wire:submit="register">

        {{-- NOMBRE --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Nombre completo
            </label>
            <div style="position: relative;">
                <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: #9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                <input wire:model="name" id="name" type="text" name="name"
                    required placeholder="User_example" autofocus autocomplete="name"
                    style="width: 100%; padding: 9px 12px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                    onfocus="this.style.borderColor='#6b8f6b'"
                    onblur="this.style.borderColor='#d4d9cc'">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- CORREO ELECTRÓNICO --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Correo electrónico
            </label>
            <div style="position: relative;">
                <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: #9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                    <polyline points="22,6 12,13 2,6" />
                </svg>
                <input wire:model="email" id="email" type="email" name="email"
                    required placeholder="example@gmail.com" autocomplete="username"
                    style="width: 100%; padding: 9px 12px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                    onfocus="this.style.borderColor='#6b8f6b'"
                    onblur="this.style.borderColor='#d4d9cc'">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- TELÉFONO --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Teléfono móvil
            </label>
            <div style="position: relative;">
                <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: #9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.82a16 16 0 0 0 6.29 6.29l.97-.97a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
                <input wire:model="phone_number" id="phone_number" type="text" name="phone_number"
                    required placeholder="600123456"
                    style="width: 100%; padding: 9px 12px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                    onfocus="this.style.borderColor='#6b8f6b'"
                    onblur="this.style.borderColor='#d4d9cc'">
            </div>
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        {{-- CONTRASEÑA --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Contraseña
            </label>
            <div x-data="{ show: false }" style="position: relative;">
                <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: #9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                <input wire:model="password" id="password" name="password"
                    :type="show ? 'text' : 'password'"
                    required placeholder="*******" autocomplete="new-password"
                    style="width: 100%; padding: 9px 40px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                    onfocus="this.style.borderColor='#6b8f6b'"
                    onblur="this.style.borderColor='#d4d9cc'">
                <button type="button" @click="show = !show"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 2px; color: #9aaa9a;"
                    onmouseover="this.style.color='#5a6b5a'"
                    onmouseout="this.style.color='#9aaa9a'">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                        <line x1="1" y1="1" x2="23" y2="23" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- CONFIRMAR CONTRASEÑA --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Confirmar contraseña
            </label>
            <div x-data="{ show: false }" style="position: relative;">
                <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: #9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation"
                    :type="show ? 'text' : 'password'"
                    required placeholder="*******" autocomplete="new-password"
                    style="width: 100%; padding: 9px 40px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                    onfocus="this.style.borderColor='#6b8f6b'"
                    onblur="this.style.borderColor='#d4d9cc'">
                <button type="button" @click="show = !show"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 2px; color: #9aaa9a;"
                    onmouseover="this.style.color='#5a6b5a'"
                    onmouseout="this.style.color='#9aaa9a'">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                        <line x1="1" y1="1" x2="23" y2="23" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- CONSENTIMIENTO LEY PROTECCIÓN DE DATOS --}}
        <div style="margin-bottom: 24px; padding: 14px 16px; background: #f7f8f5; border-radius: 8px; border: 0.5px solid #d4d9cc;">
            <label style="display: inline-flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                <input wire:model="rgpd_consent" id="rgpd_consent" type="checkbox" name="rgpd_consent" required
                    style="accent-color: #6b8f6b; width: 15px; height: 15px; margin-top: 2px; flex-shrink: 0; cursor: pointer;">
                <span style="font-size: 13px; color: #5a6b5a; line-height: 1.5;">
                    Acepto la política de privacidad y protección de datos.
                </span>
            </label>
            <x-input-error :messages="$errors->get('rgpd_consent')" class="mt-2" />
        </div>

        {{-- BOTÓN DE REGISTRO --}}
        <button type="submit"
            style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 15px; font-weight: 500; padding: 11px; border-radius: 8px; border: none; cursor: pointer; margin-bottom: 16px;"
            onmouseover="this.style.background='#4a6b4a'"
            onmouseout="this.style.background='#6b8f6b'">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <line x1="19" y1="8" x2="19" y2="14" />
                <line x1="22" y1="11" x2="16" y2="11" />
            </svg>
            Finalizar Registro
        </button>

        {{-- ENLACE AL LOGIN --}}
        <p style="text-align: center; font-size: 13px; color: #7a8a7a; margin: 0;">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" wire:navigate
                style="color: #6b8f6b; font-weight: 500; text-decoration: none;"
                onmouseover="this.style.color='#4a6b4a'"
                onmouseout="this.style.color='#6b8f6b'">
                Inicia sesión
            </a>
        </p>

    </form>
</div>