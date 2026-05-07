<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClienteMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $cliente;

    public function __construct($f)
    {
      $this->cliente = $f;
      $this->subject("Confirma tu dirección electrónica de correo");
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("noreply@shirushi.mx","Shirushi MX")
                    ->view('correos.cliente')
                    ->with(["cliente"=>$this->cliente]);
    }
}
