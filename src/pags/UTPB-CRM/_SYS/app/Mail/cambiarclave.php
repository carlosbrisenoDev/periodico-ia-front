<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class cambiarclave extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $franquiciatario;
     public $clave;

     public function __construct($f,$c)
     {
       $this->franquiciatario = $f;
       $this->clave = $c;
       $this->subject("Tu clave de acceso ha sido cambiada");
     }

     /**
      * Build the message.
      *
      * @return $this
      */
     public function build()
     {
         return $this->from("noreply@shirushi.mx","Shirushi MX")
                     ->view('correos.cambiarclave')
                     ->with(["usuario"=>$this->franquiciatario,"clave"=>$this->clave]);
     }
}
