<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // ALERTA DE BIENVENIDA TRAS LOGARSE
        session()->flash('swal', [
            'icon'  => 'success',
            'title' => '¡Hola de nuevo!',
            // 'text'  => 'Has iniciado sesión correctamente. ¿Echamos un partido?',
            'text'  => 'Has iniciado sesión correctamente!!',
        ]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">

        <!-- CORREO ELECTRÓNICO -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- CONTRASEÑA -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- BLOQUE DE ENLACE PARA RECUPERAR CONTRASEÑA Y CASILLA 'REMEMBER ME' -->
        <div class="block mt-4">
            <div class="flex items-center justify-between">

                <!-- CASILLA 'REMEMBER ME' -->
                <label for="remember" class="inline-flex items-center">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">Recuérdame</span>
                </label>

                <!-- ENLACE PARA RECUPERAR LA CONTRASEÑA -->
                @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    ¿Olvidaste la contraseña?
                </a>
                @endif
            </div>
        </div>

        <!-- ENLACES Y BOTÓN DE LOGIN -->
        <div class="flex items-center justify-between mt-4">

            <!-- ENLACE AL REGISTRO -->
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}" wire:navigate>
                ¿No tienes cuenta? Regístrate
            </a>

            <!-- BOTÓN PARA ACCEDER A LA WEB  -->
            <x-primary-button>
                Acceder
            </x-primary-button>

        </div>
    </form>
    
</div>