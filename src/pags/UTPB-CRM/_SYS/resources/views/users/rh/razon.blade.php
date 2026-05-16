@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          Rechazar solicitud
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-12 col-lg-6">
              <h4>Información del solicitante</h4>
              <div class="row">
                  <div class="col-12">
                    <label for="nombre">Describa la razón por la cual fue rechazado el solicitante</label>
                    <textarea name="razon" class="form-control" rows="8"></textarea>
                  </div>
              </div>
              <form class="" action="/empleados/rechazado" method="post">
                <input type="hidden" name="cid" value="{{$id}}">
                <div class="row">
                  <div class="col-12 col-md-12 col-lg-6">
                    <button type="submit" name="button" class="btn btn-primary">Rechazar</button>
                  </div>
                </div>
              </form>
@endsection
