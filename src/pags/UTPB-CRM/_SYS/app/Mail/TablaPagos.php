<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Tablapagos extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $tabla;

     public function __construct($tabla)
     {
       $this->tabla = $tabla;
       $this->subject("Tabla de pagos - UNISANT");
     }

     /**
      * Build the message.
      *
      * @return $this
      */
     public function build()
     {
         return $this->from("noreply@unisantorizaba.com","Unisant Orizaba")
                     ->view('correos.tabla')
                     ->with(["cliente"=>$this->tabla]);
     }
}
