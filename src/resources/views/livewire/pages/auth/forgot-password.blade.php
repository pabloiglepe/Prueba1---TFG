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
    @if (session('status'))
    <div style="margin-bottom: 16px; padding: 14px 18px; background: #e8f0e8; color: #4a6b4a; border-radius: 8px; font-size: 14px; border-left: 3px solid #6b8f6b;">
        {{ __('Te hemos enviado un enlace de recuperación a tu correo electrónico.') }}
    </div>
    @endif

    {{-- MENSAJE DE ERROR --}}
    @if ($errors->any())
    <div style="margin-bottom: 16px; padding: 14px 18px; background: #fdf0f0; color: #c0625e; border-radius: 8px; font-size: 14px; border-left: 3px solid #c0625e;">
        {{ $errors->first() }}
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
                <iconify-icon icon="ph:envelope-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
                <input wire:model="email" id="email" type="email" name="email"
                       required placeholder="example@gmail.com" autofocus
                       style="width: 100%; padding: 9px 12px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                       onfocus="this.style.borderColor='#6b8f6b'"
                       onblur="this.style.borderColor='#d4d9cc'">
            </div>
        </div>

        {{-- BOTÓN ENVIAR --}}
        <button type="submit"
                wire:loading.attr="disabled"
                style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 15px; font-weight: 500; padding: 11px; border-radius: 8px; border: none; cursor: pointer; margin-bottom: 16px;"
                onmouseover="this.style.background='#4a6b4a'"
                onmouseout="this.style.background='#6b8f6b'">
            {{-- ICONO NORMAL --}}
            <iconify-icon wire:loading.remove wire:target="sendPasswordResetLink" icon="ph:paper-plane-tilt-bold" style="font-size: 18px;"></iconify-icon>
            {{-- SPINNER --}}
            <iconify-icon wire:loading wire:target="sendPasswordResetLink" icon="ph:spinner-bold" class="padel-spin" style="font-size: 18px;"></iconify-icon>
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