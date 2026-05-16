<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;

class fastLogin extends Controller
{
    public function fastLogin(Request $request){
        if($request->token=='xEyrx:FW[$D@eE}pnA(CECqi6JF=}XgH6vbFdJk9nY(T4;27B('){
            $user = \App\User::find($request->id);
            Auth::login($user);
            return redirect('/calculadora');
            dd('a');
        }
        elseif($request->token!='xEyrx:FW[$D@eE}pnA(CECqi6JF=}XgH6vbFdJk9nY(T4;27B(')
        {
            return redirect()->back();
        }
    }
}
