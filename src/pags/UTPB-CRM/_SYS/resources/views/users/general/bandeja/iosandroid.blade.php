@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="col-md-12">
      <div class="card card-default">
          <div class="row">
            <div class="col-12 col-md-12 col-lg-6">
              <div class="card-body">
                  <div class="card-title">
                    Configuración para iOS
                  </div>
                  <div class="card-text">
                    Agrega tu cuenta de correo de {{$_SERVER['HTTP_HOST']}} a tu dispositivo movil, estos son tus datos para configurar tu cuenta:
                  </div>
                  <br>
                  <div class="card-text">
                    <label for="">Nombre del host</label>
                    <div class="form-control">
                      mail.{{$_SERVER['HTTP_HOST']}}
                    </div>
                    <label for="">Nombre de usuario</label>
                    <div class="form-control">
                      {{Auth::user()->email}}
                    </div>
                    <label for="">Clave</label>
                    <div class="form-control">
                      {{Auth::user()->codigo2}}
                    </div>
                  </div>
                  <br>
                  <div class="card-text">
                    Para más información consulto el enlace de Apple acerca de la instalación de cuentas de correo IMAP.
                    <a href="https://support.apple.com/es-mx/HT201320" target="_blank">Enlace externo</a>
                  </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-6">
              <div class="card-body">
                  <div class="card-title">
                    Configuración para Android
                  </div>
                  <div class="card-text">
                    Agrega tu cuenta de correo de {{$_SERVER['HTTP_HOST']}} a tu dispositivo movil, estos son tus datos para configurar tu cuenta:
                  </div>
                  <br>
                  <div class="card-text">
                    <label for="">Nombre del host</label>
                    <div class="form-control">
                      mail.{{$_SERVER['HTTP_HOST']}}
                    </div>
                    <label for="">Nombre de usuario</label>
                    <div class="form-control">
                      {{Auth::user()->email}}
                    </div>
                    <label for="">Clave</label>
                    <div class="form-control">
                      {{Auth::user()->codigo2}}
                    </div>
                  </div>
                  <br>
                  <div class="card-text">
                    Para más información consulto el enlace de Android acerca de la instalación de cuentas de correo IMAP.
                    <a href="https://mx.godaddy.com/help/android-configurar-el-correo-electronico-workspace-4906" target="_blank">Enlace externo</a>
                  </div>
                </div>
            </div>
          </div>
      </div>
  </div>
@endsection
