<?php

namespace App\Providers;

use App\Mail\BrevoTransport;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // FUERZA A LARAVEL A USAR HTTPS EN PRODUCCIÓN
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // CONFIGURO EL 'LOCALE' DE CARBON PARA QUE ESTÉ EN ESPAÑOL (MESE, DÍAS, ETC)
        Carbon::setLocale('es');


        Mail::extend('brevo', function () {
            return new BrevoTransport(config('services.brevo.key'));
        });


        // PERSONALIZACIÓN DEL EMAIL DE RECUPERACIÓN DE CONTRASEÑA EN ESPAÑOL
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Restablecer contraseña — PadelSync')
                ->greeting('¡Hola!')
                ->line('Has recibido este correo porque se ha solicitado restablecer la contraseña de tu cuenta.')
                ->action('Restablecer contraseña', $url)
                ->line('Este enlace caducará en ' . config('auth.passwords.users.expire') . ' minutos.')
                ->line('Si no has solicitado el restablecimiento de contraseña, no es necesario que hagas nada.')
                ->salutation('Un saludo, el equipo de PadelSync!!');
        });
    }
}
