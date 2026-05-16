<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use cPanel;
class cp extends Controller
{
  public function list(Request $r){

    $cpanel = new cPanel();
    $cpanel->getEmailAddresses();
  }
}
