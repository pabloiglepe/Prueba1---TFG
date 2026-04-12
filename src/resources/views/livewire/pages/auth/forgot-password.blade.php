<?php
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }
}; ?>

<div>
    {{-- MENSAJE DE ÉXITO --}}
    @if(session('status'))
    <div style="margin-bottom: 16px; padding: 14px 18px; background: #e8f0e8; color: #4a6b4a; border-radius: 8px; font-size: 14px; border-left: 3px solid #6b8f6b;">
        {{ __('Te hemos enviado un enlace de recuperación a tu correo electrónico.') }}
    </div>
    @endif

    <p style="font-size: 14px; color: #7a8a7a; margin: 0 0 24px; line-height: 1.6;">
        ¿Olvidaste tu contraseña? Introduce tu email y te enviaremos un enlace para restablecerla.
    </p>

    <form wire:submit="sendPasswordResetLink">

        {{-- EMAIL --}}
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Correo electrónico
            </label>
            <div style="position: relative;">
                <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: #9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
                <input wire:model="email" id="email" type="email" name="email"
                       required placeholder="example@gmail.com" autofocus
                       style="width: 100%; padding: 9px 12px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                       onfocus="this.style.borderColor='#6b8f6b'"
                       onblur="this.style.borderColor='#d4d9cc'">
            </div>
            @error('email')
                <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- BOTÓN ENVIAR --}}
        <button type="submit"
                style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 15px; font-weight: 500; padding: 11px; border-radius: 8px; border: none; cursor: pointer; margin-bottom: 16px;"
                onmouseover="this.style.background='#4a6b4a'"
                onmouseout="this.style.background='#6b8f6b'">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
            Enviar enlace de recuperación
        </button>

        {{-- ENLACE AL LOGIN --}}
        <p style="text-align: center; font-size: 13px; color: #7a8a7a; margin: 0;">
            <a href="{{ route('login') }}" wire:navigate
               style="color: #6b8f6b; font-weight: 500; text-decoration: none;"
               onmouseover="this.style.color='#4a6b4a'"
               onmouseout="this.style.color='#6b8f6b'">
                Volver al inicio de sesión
            </a>
        </p>

    </form>
</div>