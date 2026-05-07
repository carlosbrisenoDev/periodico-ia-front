@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
  <div class="col">
      <div class="card card-default large">
          <div class="card-body">
            <div class="clearfix">
              <div class="pull-left">
                <h3>Platillo</h3>
                <h4 class="titulo">{{$platillo->nombre}}</h4>
                <h5>{{($platillo->categoria != null) ? $platillo->categoria->titulo : "Sin categoria"}}</h5>
              </div>
              <div class="pull-right">
                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#data">
                  <i class="fa fa-plus"></i> Agregar imagenes
                </a>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col">
                <ul class="nolist">
                  Opciones:
                  <li>
                    <i class="fa fa-trash"></i>  Eliminar imagen
                  </li>
                  <li>
                    <i class="fas fa-screwdriver"></i>  Reparar imagen miniatura
                  </li>
                </ul>
              </div>
            </div>
            <hr>
            <div class="row">
                @if (count($platillo->imagenes) > 0)
                  @foreach ($platillo->imagenes as $imagen)
                    <div class="col-2">
                      <div class="card topp">
                        @if ($imagen->imagen == null)
                          <span class="texto">Imagen eliminada</span>
                          <img src="{{asset("images/black.png")}}" class="card-img-top" alt="{{$platillo->titulo}}">
                          @else
                            <span class="texto">{{$imagen->imagen->titulo}}</span>
                            <img src="/imagenes/watcharlittle/{{md5($imagen->imagen->id)}}" class="card-img-top" alt="{{$imagen->titulo}}">
                        @endif
                        <div class="row bg-secondary" style="margin:0;">
                          <div class="col">
                            <div class="input-group">
                              <div class="input-group">
                                <a href="/platillos/deleteimagen/{{md5($imagen->id)}}" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                                <a href="/imagenes/fixTiny/{{md5($imagen->id)}}" class="btn btn-success"><i class="fas fa-screwdriver"></i></a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                  @else
                    <div class="col">
                      No hay imagenes para este platillo.
                    </div>
                @endif
            </div>
          </div>
        </div>
      </div>
</div>
<div class="modal fade" id="data" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Seleccionar imagenes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="" action="/platillos/pi?cid={{md5($platillo->id)}}" method="post">

        <div class="row">
          @foreach (\App\imagen::all() as $imagen)
            <div class="col-2 imagen" label="{{$imagen->titulo}}">
              <label for="check{{$imagen->id}}">
                <img src="/imagenes/watcharlittle/{{md5($imagen->id)}}" class="card-img-top" alt="{{$imagen->titulo}}">
              </label>
              <input type="checkbox" name="imagenes[]" id="check{{$imagen->id}}" value="{{$imagen->id}}">
            </div>
          @endforeach
        </div>
        <input type="submit" class="hidden send" name="" value="">
        </form>
      </div>
      <div class="modal-footer">
        <div class="clearfix">
          <div class="pull-left">
            <input type="text" class="form-control buscar" placeholder="Buscar ..." value="">
          </div>
          <div class="pull-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" onclick="$('.send').click();" class="btn btn-primary">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  $(function(){
    $(".buscar").on("keyup",function(){
      var labels = $(".imagen");
      $.each(labels,function(index,el){
        if($(el).attr("label").indexOf($(".buscar").val()) != -1)
        {
          $(el).css({"display":"table-cell"});
        } else {
          $(el).css({"display":"none"});
        }
      });
    });
  })
</script>
@endsection
