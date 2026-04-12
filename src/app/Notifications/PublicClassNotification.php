<?php

namespace App\Notifications;

use App\Models\PadelClass;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PublicClassNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public PadelClass $class)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }


    /**
     * DATOS PARA EL MENSAJE DE NOTIFICACIÓN SACADOS DE LA BBDD
     *
     * @param  object $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => 'Nueva clase disponible',
            'message' => "Se ha publicado una nueva clase: \"{$this->class->title}\". ¡Inscríbete antes de que se llene!",
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
