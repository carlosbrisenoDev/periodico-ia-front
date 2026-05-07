@extends('layouts.website')
@section('content')
<div class="row">
  <div class="col-12 col-md-6 col-lg-4 col-xl-4">

  </div>
  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
    <form class="form-horizontal" method="POST" autocomplete="off" action="{{ route('login') }}">
      <div class="form-group">
        <label for="exampleInputEmail1">Correo electrónico</label>
        <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Ingresa tu correo electrónico">
        <small id="emailHelp" class="form-text text-muted">Por seguridad, nunca compartas tu acceso con nadie</small>
        @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Contraseña</label>
        <input type="password" class="form-control" name="password" id="exampleInputPassword1" placeholder="Tú contraseña">
        @if ($errors->has('password'))
            <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
      </div>
      <div class="form-group form-check">
        <input type="checkbox"  class="form-check-input" id="exampleCheck1">
        <label class="form-check-label" for="exampleCheck1">Mantenerme conectado</label>
      </div>
      <button type="submit" class="btn btn-primary">Identificarme</button>
    </form>
    <br>
  </div>
  <div class="col-12 col-md-6 col-lg-4 col-xl-4">

  </div>
</div>
@endsection
