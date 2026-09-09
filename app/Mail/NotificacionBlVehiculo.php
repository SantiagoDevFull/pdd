<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificacionBlVehiculo extends Mailable
{
    use Queueable, SerializesModels;

    public $notificaciones;
    public $title;

    public function __construct($notificaciones,$title)
    {
        $this->notificaciones = $notificaciones;
        $this->title = $title;
    }

    public function build()
    {
        return $this->subject('Notificaciones de Vehículos')
            ->view('backend.mail.notificaciones');
    }
}
