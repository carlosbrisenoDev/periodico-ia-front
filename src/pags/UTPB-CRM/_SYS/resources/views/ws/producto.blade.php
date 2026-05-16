@extends('layouts.website')
@php
  $p = \App\platillo::find($_REQUEST["cid"]);
@endphp
@section('title')
  {{$p->nombre}}
@endsection
@section('url')
{{"https://".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]}}
@endsection
@section('imagen')
@if (count($p->imagenes) > 0)
{{"https://".$_SERVER['SERVER_NAME']}}/imagenes/watchar/{{md5($p->imagenes[0]->imagen_id)}}@endif
@endsection
@section('content')
    <div class="row">
      <div class="col-12 col-md-9">
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner">
            @php
              $j = 0;
            @endphp
            @foreach ($p->imagenes as $i)
              <div class="carousel-item {{($j++ == 0) ? "active" : ""}}">
                <img src="/imagenes/watchar/{{md5($i->imagen->id)}}" class="d-block w-100">
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
        <br>
        <h2 class="title">{{$p->nombre}}</h2>
        <p>
          {{$p->descripcion}}
        </p>
        <p>
          Precio: $ <b>{{$p->precio}}</b>
        </p>
        @if ($p->envio==1)
          @if (Auth::guest())
            <a class="nav-link login" href="#" data-toggle="modal" data-target="#exampleModalCenter">Ingresa para pedir a domicilio</a>
            @else
              <div class="row">
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                  <form class="" action="/cart/add" method="post">
                  <div class="input-group">
                      <input type="hidden" class="cid" name="cid" value="{{$p->id}}">
                      <input type="number" required class="form-control pedido" name="cantidad" min="1" max="100" value="1">
                      <div class="input-group-append">
                        <input type="submit" class="btn btn-primary pedido" value="Añadir a mi pedido" />
                      </div>
                  </div>
                  </form>
                </div>
                <div class="col-8">

                </div>
              </div>
          @endif
        @else
          <div class="jumbotron">
              Este producto no esta disponible para entregas a domicilio.
          </div>
        @endif

        <hr>
        <div class="col-md-12">
          <h4>Compartir</h4>
            <div class="input-group">
              <input class="form-control url" type="text" readonly value="@yield('url',"https://".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"])">
              <div class="input-group-btn">
                <button type="button" data-clipboard-target=".url" class="btn btn-primary btn-copy">Copiar</a>
              </div>
            </div>
          </br>
          <div class="fb-like" data-href="{{"https://".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]}}" data-layout="box_count" data-action="like" data-size="small" data-show-faces="false" data-share="true"></div>
          <hr>
          <div class="fb-comments" data-href="{{"https://".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]}}" data-width="100%" data-numposts="30"></div>
        </div>
      </div>
      <div class="col-12 col-md-3">
        @php
          $sim = \App\platillo::where('visible',1)->where('categoria_id',$p->categoria_id)->orderByRAW("rand()")->limit(4)->get();
        @endphp
        <div class="row">
          <div class="col-12"  style="margin-bottom:20px;">
            <h3>Más productos</h3>
          </div>
          @foreach ($sim as $p2)
            <div class="col-12" style="margin-bottom:20px;">
              @if (count($p2->imagenes) > 0)
                <span class="imagen-menu" envio="{{$p2->envio}}"  id="{{$p2->id}}" precio="{{$p2->precio}}" titulo="{{$p2->nombre}}" imgs="@foreach ($p2->imagenes as $i),{{$i->imagen->id}}@endforeach" descripcion="{{$p2->descripcion}}">
                  <div class="card borderless">
                    <div class="image" style="background-image:url('/imagenes/watcharlittle/{{md5($p2->imagenes[0]->imagen_id)}}')"></div>
                    <div class="card-body" style="margin:0;padding:0;">
                      <br>
                      <h5 class="card-title">{{$p2->nombre}}</h5>
                      <p class="card-text">{{$p2->descripcion}}</p>
                    </div>
                  </div>
                </span>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    </div>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(".imagen-menu").on("click",function(){
      location.href = "/shirushi/producto?cid="+$(this).attr("id");
    });
  </script>
@endsection
