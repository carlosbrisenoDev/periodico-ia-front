<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReciboAtrasado extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $pago;

     public function __construct($pago)
     {
       $this->pago = $pago;
       $this->subject("Notificación de pago pendiente ".$pago->mes."/".$pago->anio." - UNISANT");
     }

     /**
      * Build the message.
      *
      * @return $this
      */
     public function build()
     {
         return $this->from("noreply@unisantorizaba.com","Unisant Orizaba")
                     ->view('correos.pagopendiente')
                     ->with(["pago"=>$this->pago]);
     }
}
