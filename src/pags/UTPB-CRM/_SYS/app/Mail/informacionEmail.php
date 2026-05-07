<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class informacionEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($asunto=null,$data,$files,$names,$subject, \App\User $user=null)
    {
        $this->data = $data;
        $this->files = $files;
        $this->names = $names;
        $this->subject = $subject;
        $this->mail = $user->email;
        $this->nameu = $user->name;
        $this->asunto = $asunto;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      foreach ($this->files as $key => $file) {
        $this->attach($file,["as"=>$this->names[$key]]);
      }
      
      return $this->subject($this->asunto ?? "Aquí esta la información solicitada ".\Carbon\carbon::now())
              ->from("webmaster@e-dav.net", $this->subject ?? "eCustomerManager")
              ->replyTo($this->mail,$this->nameu)
              ->view('correos.data')->with(["data"=>$this->data]);
    }
}
