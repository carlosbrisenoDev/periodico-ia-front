<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VideoFirma extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $cartera;

     public function __construct($cartera)
     {
       $this->cartera = $cartera;
       $this->subject("VideoFirma - UNISANT");
     }

     /**
      * Build the message.
      *
      * @return $this
      */
     public function build()
     {
         return $this->from("noreply@unisantorizaba.com","Unisant Orizaba")
                     ->view('correos.cartera')
                     ->with(["cliente"=>$this->cartera]);
     }
}
