<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevoCliente extends Mailable
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
    public $cliente;
    public function __construct($asunto,$cliente,$user)
    {
        $this->subject = 'Nuevo Cliente Asignado';
        $this->asunto = $asunto;
        $this->user = $user;
        $this->cliente = $cliente;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this->from("webmaster@e-dav.net", $this->subject ?? "Nuevo Cliente Asignado")
              ->subject($this->asunto ?? "Se te ha asignado un nuevo cliente -  ".\Carbon\carbon::now())
              ->view('correos.asesor')->with(["user"=>$this->user,"cliente"=>$this->cliente]);
    }
}
