@extends('layouts.website')
@section('content')
  <div class="row">
    <div class="col-12 col-md-6">
      <div class="heading">
        <h3>
        </h3>
        <div class="h">

        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <img src="{{ asset('images/cliente.jpg')}}" class="img">
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <p class="text gray" align="justify">
        Registrate con nosotros y obten acceso a exclusivo, pide a domicilio y más.
      </p>
      <div class="card borderless">
        <div class="card-body">
          <form class="" action="/usuarios/registro" method="post">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail4">Nombre completo</label>
              <input type="text" class="form-control" name="nombre" required id="inputEmail4" placeholder="Nombre completo">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail4">Correo electrónico</label>
              <input type="email" class="form-control" name="correo" required id="inputEmail4" placeholder="Correo electrónico">
            </div>
          </div>
          <div class="form-group">
            @if (Session::has("error"))
              <div class="badge badge-primary">
                {{Session::get("error")}}
              </div>
            @endif
          </div>
          <div class="form-group">
            Fecha de nacimiento
            <div class="row">
              <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                <label for="inputAddress">Día</label>
                <select class="form-control" name="dia">
                  @for ($i=1; $i < 32; $i++)
                    <option value="{{$i}}">{{$i}}</option>
                  @endfor
                </select>
              </div>
              <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                <label for="inputAddress">Mes</label>
                <select class="form-control" name="mes">
                  @php
                    $meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
                  @endphp
                  @for ($i=0; $i < 12; $i++)
                    <option value="{{$i}}">{{$meses[$i]}}</option>
                  @endfor
                </select>              </div>
              <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                <label for="inputAddress">Año</label>
                <select class="form-control" name="anio">
                  @for ($i=Date("Y"); $i > 1910; $i--)
                    <option value="{{$i}}">{{$i}}</option>
                  @endfor
                </select>              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="inputAddress2">Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="inputAddress2" placeholder="(555) 228 00 00)">
          </div>
          <div class="card">
            <div class="card-body">
              <div class="form-group">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="gridCheck" required>
                  <label class="form-check-label" for="gridCheck">
                    He leído el <a href="/shirushi/avisodeprivacidad">aviso de privacidad</a> y quiero que me envien un correo electrónico con más información.
                  </label>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Registrarme</button>
            </div>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
@endsection
