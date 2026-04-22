<?php
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token'    => ['required'],
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password'       => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status));
        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div>
    {{-- MENSAJE DE ERROR --}}
    @if ($errors->any())
    <div style="margin-bottom: 16px; padding: 14px 18px; background: #fdf0f0; color: #c0625e; border-radius: 8px; font-size: 14px; border-left: 3px solid #c0625e;">
        {{ $errors->first() }}
    </div>
    @endif

    <p style="font-size: 14px; color: #7a8a7a; margin: 0 0 24px; line-height: 1.6;">
        Introduce tu email y elige una nueva contraseña para tu cuenta.
    </p>

    <form wire:submit="resetPassword">

        {{-- EMAIL --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Correo electrónico
            </label>
            <div style="position: relative;">
                <iconify-icon icon="ph:envelope-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
                <input wire:model="email" id="email" type="email" name="email"
                       required autofocus autocomplete="username"
                       style="width: 100%; padding: 9px 12px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                       onfocus="this.style.borderColor='#6b8f6b'"
                       onblur="this.style.borderColor='#d4d9cc'">
            </div>
        </div>

        {{-- NUEVA CONTRASEÑA --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Nueva contraseña
            </label>
            <div x-data="{ show: false }" style="position: relative;">
                <iconify-icon icon="ph:lock-open-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
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
        </div>

        {{-- CONFIRMAR CONTRASEÑA --}}
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                Confirmar nueva contraseña
            </label>
            <div x-data="{ show: false }" style="position: relative;">
                <iconify-icon icon="ph:lock-open-bold" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #9aaa9a; pointer-events: none;"></iconify-icon>
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
        </div>

        {{-- BOTÓN RESTABLECER --}}
        <button type="submit"
                wire:loading.attr="disabled"
                style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 15px; font-weight: 500; padding: 11px; border-radius: 8px; border: none; cursor: pointer;"
                onmouseover="this.style.background='#4a6b4a'"
                onmouseout="this.style.background='#6b8f6b'">
            {{-- ICONO NORMAL --}}
            <iconify-icon wire:loading.remove wire:target="resetPassword" icon="ph:key-bold" style="font-size: 18px;"></iconify-icon>
            {{-- SPINNER --}}
            <iconify-icon wire:loading wire:target="resetPassword" icon="ph:spinner-bold" class="padel-spin" style="font-size: 18px;"></iconify-icon>
            Restablecer contraseña
        </button>

    </form>
</div>