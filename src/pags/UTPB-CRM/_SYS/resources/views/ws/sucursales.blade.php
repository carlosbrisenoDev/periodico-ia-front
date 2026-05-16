@extends('layouts.website')
@section('content')
<div class="row">
  <div class="col">
    <h3>
      SUCURSALES
    </h3>
    <br>
    <div class="row">
      <div class="col-12">
        <iframe style="border:none;" src="https://www.google.com/maps/d/embed?mid=1ZeN0aqXL5XEVliyrhquFEPNjyDN7iaOP" width="100%" height="480"></iframe>
      </div>
    </div>
    <br><br>
    <div class="row">
    @foreach (\App\sucursal::groupBy('estado')->orderBy("estado","desc")->get() as $estado)
        @foreach (\App\sucursal::where('visible',1)->where('estado',$estado->estado)->get() as $suc)
          <div class="col-12 col-sm-4" style="margin-bottom:20px;">
            <div class="card">
              <div class="hoverca">
                {{$suc->estado}}
              </div>
              <div id="carouselExampleControls" class="carousel slide card-img-top" data-ride="carousel">
                <div class="carousel-inner">
                  @if (count($suc->imagenes) == 0)
                    <div class="carousel-item active">
                        <img src="{{asset("images/black.png")}}" class="img" alt="{{$suc->titulo}}">
                    </div>
                  @endif
                  @foreach ($suc->imagenes as $imagen)
                    <div class="carousel-item active">
                        <img src="/imagenes/watchar/{{md5($imagen->imagen->id)}}" class="img" alt="{{$imagen->titulo}}">
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
              <div class="card-body">
                <p class="card-text">
                  <h5 class="title">
                    <a href="/shirushi/sucursal/{{$suc->nombre}}">{{$suc->nombre}}</a>
                  </h5>
                </br>{{$suc->direccion}}
              </br>{{$suc->horario}}
                </p>
                <p class="card-text">
                  <p class="tinytext">
                    @foreach (explode(",",$suc->telefono) as $i => $t)
                      <a href="tel:{{$t}}">{{$t}}</a>
                    @endforeach
                  </p>
                </p>
              </div>
            </div>
          </div>
        @endforeach
    @endforeach
  </div>
  </div>
</div>


@endsection
