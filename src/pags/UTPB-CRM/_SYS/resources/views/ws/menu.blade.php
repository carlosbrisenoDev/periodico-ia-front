@extends('layouts.website')
@section('content')
    <div class="text-slider">
      @foreach (\App\categoria::all() as $categoria)
            <button class="nbtn" type="button" data-toggle="collapse" data-target="#collapse{{$categoria->id}}" aria-expanded="false" aria-controls="collapseOne">
              <div class="float-left">
                {{$categoria->titulo}}
              </div>
            </button>
      @endforeach
    </div>
    <div class="accordion" style="margin:0;padding:0;" id="accordionExample">
      <div class="card borderless">
        @php
          $j = 1;
        @endphp
        @foreach (\App\categoria::all() as $categoria)
          @php
            $els = \App\platillo::where('categoria_id',$categoria->id)->get();
          @endphp
          <div id="collapse{{$categoria->id}}" class="collapse {{($j++==2) ? "show" : ""}}" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-body">
              <div class="row">
              @foreach ($els as $p)
                @if($p->visible ==1)
                  <div class="col-12 col-md-3">
                    @if (count($p->imagenes) > 0)
                      <span class="imagen-menu" envio="{{$p->envio}}"  id="{{$p->id}}" precio="{{$p->precio}}" titulo="{{$p->nombre}}" imgs="@foreach ($p->imagenes as $i),{{$i->imagen->id}}@endforeach" descripcion="{{$p->descripcion}}">
                        <div class="card borderless">
                          <div class="image" style="background-image:url('/imagenes/watcharlittle/{{md5($p->imagenes[0]->imagen_id)}}')"></div>
                          <div class="card-body">
                            <h6 class="card-title">{{$p->nombre}}</h6>
                          </div>
                        </div>
                      </span>
                    @endif
                  </div>
                @endif
              @endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

@endsection
@section('scripts')
  <script type="text/javascript">
    $(function(){
      $('.text-slider').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        centerMode: true,
        variableWidth: true
      });
    });
    $(".imagen-menu").on("click",function(){
      location.href = "/shirushi/producto?cid="+$(this).attr("id");
    });
  </script>
@endsection
@section('modal')
  <div class="modal menu fade" id="data" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Seleccionar imagenes</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <div class="modal-body">
      <div class="row">
        <div class="col-xs-12 col-sm-6">
          <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">

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
        <div class="col-xs-12 col-sm-6">
          <p class="jutify descripcion">

          </p>
          <p>
            <b>Precio:</b> $ <span class="precio"></span>
          </p>
          <p class="disponible hidden jumbotron">
            No disponible en pedidos a domicilio
          </p>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <div class="clearfix">
        <div class="pull-left">

          <form class="" action="/cart/add" method="post">
          <div class="input-group">
              <input type="hidden" class="cid" name="cid" value="">
              <input type="number" required class="form-control pedido" name="cantidad" min="1" max="100" value="1">
              <div class="input-group-append">
                <input type="submit" class="btn btn-primary pedido" value="Añadir a mi pedido" />
              </div>
              <div class="input-group-append">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              </div>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection
