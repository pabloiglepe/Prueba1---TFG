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
                <iconify-icon icon="ph:user-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
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
                <iconify-icon icon="ph:envelope-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
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
                <iconify-icon icon="ph:phone-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
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
                <iconify-icon icon="ph:lock-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
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
                    <iconify-icon x-show="!show" icon="ph:eye-bold" style="font-size: 16px;"></iconify-icon>
                    <iconify-icon x-show="show" icon="ph:eye-slash-bold" style="font-size: 16px;"></iconify-icon>
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
                <iconify-icon icon="ph:lock-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
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
                    <iconify-icon x-show="!show" icon="ph:eye-bold" style="font-size: 16px;"></iconify-icon>
                    <iconify-icon x-show="show" icon="ph:eye-slash-bold" style="font-size: 16px;"></iconify-icon>
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
            <iconify-icon icon="ph:user-plus-bold" style="font-size: 18px;"></iconify-icon>
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