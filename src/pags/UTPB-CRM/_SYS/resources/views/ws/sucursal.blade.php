@extends('layouts.website')
@section('content')
<div class="row">
  <div class="col-12 col-md-6">
      <div class="card border-light">
        <div class="card-body">
          <h3>{{strtoupper($suc->nombre)}}</h3>
          <iframe src="{{$suc->iframe}}" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
          <hr>
        <h5>Formulario de contacto</h5>
        <div class="row">
          <div class="col-12"> 
            <label for="nombre">Nombre del cliente</label>
            <input class="form-control" type="text" name="nombre" value="" placeholder="Nombre del cliente" required autofocus>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <label for="nombre">Teléfono</label>
            <input type="text" class="form-control" name="telefono" value="" placeholder="Teléfono" required autofocus>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <label for="nombre">Correo electrónico</label>
            <input type="text" class="form-control" name="correo" value="" placeholder="contacto@algo.com" required autofocus>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <label for="nombre">Información adicional</label>
            <textarea name="correo"  class="form-control"value="" placeholder="Quiero ..." required autofocus></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <br>
            <button type="button" class="btn btn-primary" name="button"> <i class="fa fa-envelopment"></i> Contactar</button>
          </div>
        </div>
      </div>
    </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card border-light">
          <div class="card-body">
            <div class="row" style="margin-top:40px;">
              <div class="col-12">
                <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                  <div class="carousel-inner">
                    @foreach ($suc->imagenes as $imagen)
                      <div class="carousel-item active">
                        @if ($imagen->imagen == null)
                          <img src="{{asset("images/black.png")}}" class="d-block" alt="{{$sucursal->titulo}}">
                          @else
                            <img src="/imagenes/watchar/{{md5($imagen->imagen->id)}}" class="img" alt="{{$imagen->titulo}}">
                        @endif
                      </div>
                    @endforeach
                  </div>
                  <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Anterior</span>
                  </a>
                  <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Siguiente</span>
                  </a>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <img src="{{asset("/images/logo.png")}}" style="width:100%" alt="">
              </div>
            </div>
            <div class="row">
              <div class="col">
                {{$suc->nombre}}
                <p class="tel">
                  {{$suc->direccion}}
                </p>
              </div>
              <div class="col–4">
                <div class="col–4">
                  <img src="{{asset("/images/qr.jpg")}}" alt="">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col nopadding">
                <p class="tinytext">
                  @foreach (explode(",",$suc->telefono) as $i => $t)
                    <a href="tel:{{$t}}">{{$t}}</a>
                  @endforeach
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col nopadding">
                <p class="tinytext">
                  {{$suc->correo}}
                </p>
              </div>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection
