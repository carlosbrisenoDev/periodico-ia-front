@extends('layouts.website')
@section('content')
  <div class="row">
    <div class="col-12 col-md-12 col-lg-6">
      <div class="heading h100">
        <h3>
          CONCLUYE TU REGISTRO
        </h3>
        <div class="h">

        </div>
      </div>
      <div class="row">
        <div class="col">
            <p class="text" align="justify">
              Registrate con nosotros y obten acceso a exclusivo, pide a domicilio y más.
            </p>
          <img src="{{ asset('images/cliente.jpg')}}" class="img">
        </div>
      </div>
    </div>
    <div class="col-12 col-md-12 col-lg-6">
      <div class="h100">
      </div>
      <div class="card">
        <div class="card-body">
          <form class="" action="/usuarios/concluir" method="post">
          <div class="form-row">
            <div class="form-group">
              <h3>Escribe la clave de acceso para Shirushi.</h3>
              <hr>
              <label for="inputEmail4">Clave de acceso</label>
              <input type="hidden" name="cid" value="{{$cid}}">
              <input type="text" class="form-control" name="password" required id="inputEmail4" placeholder="Clave de acceso">
            </div>
          </div>
          <center>
            <button type="submit" class="btn btn-primary">Concluir mi registro</button>
          </center>
        </form>
        </div>
      </div>
    </div>
  </div>
@endsection
