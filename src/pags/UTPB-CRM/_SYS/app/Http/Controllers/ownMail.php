<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Swift_Message;
use View;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ownMail extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function send($view,$info,$to,$name,$subject)
    {
      $myView =  View::make($view,$info);
      $myViewContent = $myView->render();
      $myViewContent = preg_replace('/\r?\n|\r/','<br/>', $myViewContent);
      $myViewContent = str_replace(array("\r\n","\r","\n"),"<br/>", $myViewContent);
      $myViewContent = nl2br($myViewContent);
      try{
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', '25', 'tls');
        $transport->setUsername('loloco2000@gmail.com');
        $transport->setPassword('Sg3210la');
        $mailer = \Swift_Mailer::newInstance($transport);
        $message = Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setBody($myViewContent,'text/html');
        $message->setFrom(array('loloco2000@gmail.com' => 'No Reply'));
        $message->setTo(array($to => $name));
        $result = $mailer->send($message, $failures);
        $mailer->getTransport()->start();
        } catch (Swift_TransportException $e) {
            dd($e->getMessage());
        } catch (Exception $e) {
          dd($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
