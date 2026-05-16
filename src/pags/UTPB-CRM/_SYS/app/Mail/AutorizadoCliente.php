<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AutorizadoCliente extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $cliente;
     public $usuario;

     public function __construct($f,$u)
     {
       $this->cliente = $f;
       $this->usuario = $u;
       $this->subject("Su cuenta de inscripción esta lista");
     }

     /**
      * Build the message.
      *
      * @return $this
      */
     public function build()
     {
         return $this->from("noreply@unisantorizaba.com","Unisant Orizaba")
                     ->view('correos.autorizadocliente')
                     ->with(["cliente"=>$this->cliente,
                              "usuario"=>$this->usuario]);
     }
}
