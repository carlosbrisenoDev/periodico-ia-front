<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Autorizado extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $franquiciatario;
     public $usuario;

     public function __construct($f,$u)
     {
       $this->franquiciatario = $f;
       $this->usuario = $u;
       $this->subject("Su solicitud de Franquiciatario ha sido autorizada");
     }

     /**
      * Build the message.
      *
      * @return $this
      */
     public function build()
     {
         return $this->from("noreply@shirushi.mx","Shirushi MX")
                     ->view('correos.autorizado')
                     ->with(["franq"=>$this->franquiciatario,
                              "usuario"=>$this->usuario]);
     }
}
