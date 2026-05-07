<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RemoveCliente extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $subject;
    public $asunto;
    public $user;
    public $cliente;
    public function __construct($asunto,$cliente,$user)
    {
        $this->subject = 'Reasignación de Cliente';
        $this->asunto = $asunto;
        $this->user = $user;
        $this->cliente = $cliente;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this->from("webmaster@e-dav.net", $this->subject ?? "Reasignación de Cliente")
              ->subject($this->asunto ?? "Dejas de ser asesor de un cliente -  ".\Carbon\carbon::now())
              ->view('correos.quitasesor')->with(["user"=>$this->user,"cliente"=>$this->cliente]);
    }
}
