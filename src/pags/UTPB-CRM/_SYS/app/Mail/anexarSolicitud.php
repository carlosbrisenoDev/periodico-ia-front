<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class anexarSolicitud extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $franquiciatario;

     public function __construct($f)
     {
       $this->franquiciatario = $f;
       $this->subject("Solicitud anexada");
     }

     /**
      * Build the message.
      *
      * @return $this
      */
     public function build()
     {
         return $this->from("noreply@shirushi.mx","Shirushi MX")
                     ->view('correos.solicitar')
                     ->with(["franq"=>$this->franquiciatario]);
     }
}
