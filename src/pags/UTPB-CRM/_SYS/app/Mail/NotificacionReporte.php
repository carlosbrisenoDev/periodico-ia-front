<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionReporte extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $subject;
    public $asunto;
    public $user;
    public $creador;
    public $reporte;
    public function __construct($asunto,$subject,$creador,$user,$reporte)
    {
        $this->subject = $subject;
        $this->asunto = $asunto;
        $this->creador = $creador;
        $this->user = $user;
        $this->reporte = $reporte;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this->from("webmaster@e-dav.net", $this->subject ?? "Notificación Reporte")
              ->subject($this->asunto ?? "Se te ha asignado un nuevo reporte -  ".\Carbon\carbon::now())
              ->view('correos.reporte')->with(["user"=>$this->user,"creador"=>$this->creador,"reporte"=>$this->reporte]);
    }
}
