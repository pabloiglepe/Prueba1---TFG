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
                <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: #9aaa9a; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                    <polyline points="22,6 12,13 2,6" />
                </svg>
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
        {{-- CONTRASEÑA --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Contraseña
            </label>
            <div x-data="{ show: false }" style="position: relative;">
                <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: #9aaa9a; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
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
            <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                <polyline points="10 17 15 12 10 7" />
                <line x1="15" y1="12" x2="3" y2="12" />
            </svg>
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