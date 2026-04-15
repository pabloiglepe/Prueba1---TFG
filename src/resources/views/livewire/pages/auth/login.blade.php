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
            'text'  => 'Has iniciado sesión correctamente!!',
        ]);

        // MANEJO DE LA RUTA SEGÚN EL ROL TRAS LOGARSE
        $role = \Illuminate\Support\Facades\Auth::user()->role->name;
        $route = match ($role) {
            'admin'  => route('home', absolute: false),
            'coach'  => route('home', absolute: false),
            'player' => route('home', absolute: false),
            default  => route('home', absolute: false),
        };

        $this->redirectIntended(default: $route, navigate: true);
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">

        {{-- CORREO ELECTRÓNICO --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Correo electrónico
            </label>
            <div style="position: relative;">
                <iconify-icon icon="ph:envelope-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
                <input wire:model="form.email"
                    id="email" type="email" name="email"
                    required placeholder="example@gmail.com" autofocus autocomplete="username"
                    style="width: 100%; padding: 9px 12px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                    onfocus="this.style.borderColor='#6b8f6b'"
                    onblur="this.style.borderColor='#d4d9cc'">
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        {{-- CONTRASEÑA --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Contraseña
            </label>
            <div x-data="{ show: false }" style="position: relative;">
                <iconify-icon icon="ph:lock-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
                <input wire:model="form.password"
                    id="password" name="password"
                    :type="show ? 'text' : 'password'"
                    required placeholder="*******" autocomplete="current-password"
                    style="width: 100%; padding: 9px 40px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                    onfocus="this.style.borderColor='#6b8f6b'"
                    onblur="this.style.borderColor='#d4d9cc'">
                {{-- BOTÓN VER/OCULTAR --}}
                <button type="button" @click="show = !show"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 2px; color: #9aaa9a;"
                    onmouseover="this.style.color='#5a6b5a'"
                    onmouseout="this.style.color='#9aaa9a'">
                    <iconify-icon x-show="!show" icon="ph:eye-bold" style="font-size: 16px;"></iconify-icon>
                    <iconify-icon x-show="show" icon="ph:eye-slash-bold" style="font-size: 16px;"></iconify-icon>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        {{-- BLOQUE DE ENLACE PARA RECUPERAR CONTRASEÑA Y CASILLA 'REMEMBER ME' --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">

            {{-- CASILLA 'REMEMBER ME' --}}
            <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a; cursor: pointer;">
                <input wire:model="form.remember" id="remember" type="checkbox" name="remember"
                    style="accent-color: #6b8f6b; width: 15px; height: 15px; cursor: pointer;">
                Recuérdame
            </label>

            {{-- ENLACE PARA RECUPERAR LA CONTRASEÑA --}}
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" wire:navigate
                style="font-size: 13px; color: #6b8f6b; text-decoration: none;"
                onmouseover="this.style.color='#4a6b4a'"
                onmouseout="this.style.color='#6b8f6b'">
                ¿Olvidaste la contraseña?
            </a>
            @endif
        </div>

        {{-- BOTÓN DE LOGIN --}}
        <button type="submit"
            style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 15px; font-weight: 500; padding: 11px; border-radius: 8px; border: none; cursor: pointer; margin-bottom: 16px;"
            onmouseover="this.style.background='#4a6b4a'"
            onmouseout="this.style.background='#6b8f6b'">
            <iconify-icon icon="ph:sign-in-bold" style="font-size: 18px;"></iconify-icon>
            Acceder
        </button>

        {{-- ENLACE AL REGISTRO --}}
        <p style="text-align: center; font-size: 13px; color: #7a8a7a; margin: 0;">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" wire:navigate
                style="color: #6b8f6b; font-weight: 500; text-decoration: none;"
                onmouseover="this.style.color='#4a6b4a'"
                onmouseout="this.style.color='#6b8f6b'">
                Regístrate
            </a>
        </p>

    </form>
</div>