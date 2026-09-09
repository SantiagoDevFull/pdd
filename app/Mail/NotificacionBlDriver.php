<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionBlDriver extends Mailable
{
    use Queueable, SerializesModels;

    public $notificaciones_drivers;
    public $title;

    public function __construct($notificaciones_drivers,$title)
    {
        $this->notificaciones_drivers = $notificaciones_drivers;
        $this->title = $title;
    }

    public function build()
    {
        return $this->subject('Notificaciones de Choferes')
            ->view('backend.mail.drivers');
    }
}
