@extends('layouts.app')
@section('content')

            <div class="col-md-12">
              <div class="card text-center">
              <div class="card-header">
                <img src="{{asset("images/server/404.png")}}" class="img-fluid" style="width:200px;">
                <br>
                <br>
                <h2>404</h2>
              </div>
              <div class="card-body">
                <h5 class="card-title">¡Ups!, Contenido no disponible</h5>
                <p class="card-text">Tenemos dificultades tecnicas o nos encontramos en mantenimiento</p>
                <a href="/" class="btn btn-primary">Volver atrás</a>
              </div>
              <div class="card-footer text-muted">
                Esta acción será almacenada en tu historial
              </div>
              </div>
            </div>
@endsection
