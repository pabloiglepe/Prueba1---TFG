<?php

namespace App\Notifications;

use App\Models\PadelClass;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassRegistrationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected PadelClass $padelClass)
    {
        //
    }

    /**
     * CANALES DE NOTIFICACIÓN QUE ESTÁN ACTIVOS
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {

        // return ['mail'];
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->subject('Inscripción confirmada - ' . $this->padelClass->title)
    //         ->greeting('¡Hola, ' . $notifiable->name . '!')
    //         ->line("Has sido inscrito en la clase \"{$this->padelClass->title}\".")
    //         ->line('Fecha: ' . $this->padelClass->date)
    //         ->line('Hora: ' . $this->padelClass->start_time)
    //         ->line('Entrenador: ' . $this->padelClass->coach->name)
    //         ->line('Pista: ' . $this->padelClass->court->name)
    //         ->action('Ver mis clases', url('/player/classes'))
    //         ->line('¡Nos vemos en la pista!');
    // }

    
    /**
     * DATOS PARA EL MENSAJE DE NOTIFICACIÓN SACADOS DE LA BBDD
     *
     * @param  object $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => '¡Inscripción confirmada!',
            'message' => "Has sido inscrito en la clase \"{$this->padelClass->title}\".",
            'class_id'     => $this->padelClass->id,
            'class_title'  => $this->padelClass->title,
            'class_date'   => $this->padelClass->date,
            'class_time'   => $this->padelClass->start_time,
            'coach'        => $this->padelClass->coach->name,
            'court'        => $this->padelClass->court->name,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
