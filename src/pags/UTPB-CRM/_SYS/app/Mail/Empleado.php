<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Empleado extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $empleado;

     public function __construct($f)
     {
       $this->empleado = $f;
       $this->subject("Confirma tu registro de empleado");
     }

     /**
      * Build the message.
      *
      * @return $this
      */
     public function build()
     {
         return $this->from("noreply@shirushi.mx","Shirushi MX")
                     ->view('correos.empleado')
                     ->with(["empleado"=>$this->empleado]);
     }
}
