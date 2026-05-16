<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PreAprobarCliente extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $cliente;
     public $razon;

     public function __construct($f,$r)
     {
       $this->cliente = $f;
       $this->razon = $r;
       $this->subject("Tu solicitud ha sido pre aprobada");
     }

     /**
      * Build the message.
      *
      * @return $this
      */
     public function build()
     {
         return $this->from("noreply@unisantorizaba.com","Unisant Orizaba ")
                     ->view('correos.aprobar')
                     ->with(["razon"=>$this->razon,"cliente"=>$this->cliente]);
     }
}
