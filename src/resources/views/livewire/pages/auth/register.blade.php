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
                'name' => ['required', 'string', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:100', 'unique:' . User::class],
                'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
                'phone_number' => ['required', 'numeric', 'digits:9'],
                'rgpd_consent' => ['accepted'],
            ],
            // MENSAJES DE ERROR CUSTOM 
            [
                // NOMBRE
                'name.required' => 'El nombre completo debe venir relleno.',

                // TELÉFONO
                'phone_number.required' => 'El Teléfono es obligatorio para avisarte si hay cambios en la pista.',
                'phone_number.numeric' => 'El Teléfono solo admite números.',
                'phone_number.digits' => 'El Teléfono no admite más de 9 caractéres',

                // EMAIL
                'email.unique' => 'Este Correo Electrónico ya está registrado en el club.',

                // LEY PROTECCIÓN DE DATOS
                'rgpd_consent.accepted' => 'Debes aceptar la ley de protección de datos para poder continuar.',

                // CONTRASEÑA
                'password.confirmed' => 'Las contraseñas deben coincidir.'

            ],

        );

        // ASIGNO POR DEFECTO EL ROL DE PLAYER
        $playerRole = Role::where('name', 'player')->first();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone_number' => $this->phone_number,
            'rgpd_consent' => $this->rgpd_consent,
            'role_id' => $playerRole->id,
        ]);


        event(new Registered($user));

        Auth::login($user);

        // CREAMOS LA ALERTA DE REGISTRO USANDO LA LIBRERÍA SWEETALERT2
        // $this->dispatch('swal:success', [
        //     'title' => '¡Bienvenido al Club!',
        //     'text' => 'Tu registro ha sido un éxito. ¡A jugar!',
        //     'icon' => 'success'
        // ]);

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


<!-- VISTA -->
<div>
    <form wire:submit="register">
        <!-- NOMBRE -->
        <div>
            <x-input-label for="name" :value="__('Nombre Completo')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- CORREO ELECTRÓNICO -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- NÚMERO DE TELÉFONO -->
        <div class="mt-4">
            <x-input-label for="phone_number" :value="__('Teléfono Móvil')" />
            <x-text-input wire:model="phone_number" id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" required placeholder="Ej: 600123456" />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        <!-- CONTRASEÑA -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- CONFIRMAR CONTRASEÑA -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- CONSENTIMIENTO LEY PROTECCIÓN DE DATOS -->
        <div class="block mt-4">
            <label for="rgpd_consent" class="inline-flex items-center">
                <input wire:model="rgpd_consent" id="rgpd_consent" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="rgpd_consent" required>
                <span class="ms-2 text-sm text-gray-600">Acepto la política de privacidad y protección de datos.</span>
            </label>
            <x-input-error :messages="$errors->get('rgpd_consent')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('¿Ya tienes cuenta?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Finalizar Registro') }}
            </x-primary-button>
        </div>
    </form>
</div>

<script>
    window.addEventListener('swal:success', event => {
        Swal.fire({
            title: event.detail[0].title,
            text: event.detail[0].text,
            icon: event.detail[0].icon,
            confirmButtonColor: '#4f46e5',
        });
    });
</script>