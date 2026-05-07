<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Rechazado extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $empleado;
     public $razon;

     public function __construct($f,$r)
     {
       $this->empleado = $f;
       $this->razon = $r;
       $this->subject("Tu solicitud ha sido rechazada");
     }

     /**
      * Build the message.
      *
      * @return $this
      */
     public function build()
     {
         return $this->from("noreply@shirushi.mx","Shirushi MX")
                     ->view('correos.rechazado')
                     ->with(["franq"=>$this->empleado,"razon"=>$this->razon]);
     }
}
