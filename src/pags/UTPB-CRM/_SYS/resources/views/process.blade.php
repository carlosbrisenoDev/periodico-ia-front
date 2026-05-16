<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <title>Firma de crédito estudiantil</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.2/moment.min.js"></script>
    <style media="screen">
      .camera{
        width:100%;
        height:auto;
      }
    </style>
  </head>
  <body>

    <div class="container-fluid">
      <div class="row">
        <div class="col">

        </div>
        <div class="col-xs-12 col-md-4">
          <br>
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Video firma</h5>
              <hr>
              <img class="img-fluid" src="{{asset('images/logo.png')}}" alt="">
              <hr>
              <div class="alert alert-info">
                <p align="justify">
                  Tu video firma ha sido enviada al departamento de crédito para su validación, en breve serás notificado de su estado.
                </p>
              </div>
              <hr>
              <p class="text-center">
                <small class="text-center">Regresa a tu computadora o cierra esta pestaña.</small>
                <br>
                <br>
                <a href="https://sii.unisantorizaba.com" class="btn btn-primary">Terminar</a>
              </p>
            </div>
          </div>
        </div>
        <div class="col">

        </div>
      </div>
      </div>
    </div>

  </body>
</html>
