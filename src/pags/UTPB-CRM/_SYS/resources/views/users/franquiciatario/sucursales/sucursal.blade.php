@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                    <h3><i class="fas fa-store-alt"></i> {{$sucursal->nombre}}</h3>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/sucursales/actualizardos">
                      <div class="row">
                        <div class="col-12 col-md-12 col-lg-6">
                              {{ csrf_field() }}

                              <div class="{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                  <label for="nombre" class="control-label">Nombre de la sucursal</label>

                                      <input placeholder="Nombre" disabled value="{{$sucursal->nombre}}" id="nombre" type="text" class="form-control large" >

                                      @if ($errors->has('nombre'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('nombre') }}</strong>
                                          </span>
                                      @endif
                              </div>

                        </div>
                        <div class="col-12 col-md-12 col-lg-6">
                              <div class="{{ $errors->has('alias') ? ' has-error' : '' }}">
                                  <label for="alias" class="control-label">Alias</label>
                                      <input placeholder="Alias"  value="{{$sucursal->alias}}" id="alias" type="text" class="form-control large" name="alias" required autofocus>
                                      @if ($errors->has('alias'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('alias') }}</strong>
                                          </span>
                                      @endif
                              </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-6">
                              {{ csrf_field() }}

                              <div class="{{ $errors->has('estado') ? ' has-error' : '' }}">
                                  <label for="estado" class="control-label">Estado</label>

                                      <input placeholder="Estado" disabled required value="{{$sucursal->estado}}" id="estado" type="text" class="form-control large" name="estado" required autofocus>

                                      @if ($errors->has('estado'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('estado') }}</strong>
                                          </span>
                                      @endif
                              </div>

                        </div>
                        <div class="col-12 col-md-12 col-lg-6">
                          <div class="{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="email" class="control-label">Correo electrónico de contacto</label>

                                  <input type="email" name="correo" class="form-control large" value="{{$sucursal->correo}}" required  placeholder="contacto{{"@"}}{{$_SERVER['HTTP_HOST']}}">
                                  <input type="hidden" name="sucursal_id" value="{{$sucursal->id}}">
                                  @if ($errors->has('email'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span>
                                  @endif
                          </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12 col-md-12 col-lg-6">

                            <div class="{{ $errors->has('direccion') ? ' has-error' : '' }}">
                                <label for="direccion" class="control-label">
                                  Dirección del local
                                </label>
                                    <textarea class="form-control large" style="height:150px;" placeholder="Dirección" name="direccion" required autofocus>{{$sucursal->direccion}}</textarea>

                                    @if ($errors->has('direccion'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('direccion') }}</strong>
                                        </span>
                                    @endif
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-6">
                          <div class="{{ $errors->has('telefono') ? ' has-error' : '' }}">
                              <label for="telefono" class="control-label">
                                Teléfonos (Separados por ,)
                              </label>
                                  <textarea class="form-control large" style="height:150px;" placeholder="555 000 00 00, 555 111 11 11, ..." name="telefono" required autofocus>{{$sucursal->telefono}}</textarea>

                                  @if ($errors->has('telefono'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('telefono') }}</strong>
                                      </span>
                                  @endif
                          </div>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-12 col-md-12 col-lg-6">
                        <div class="{{ $errors->has('horario') ? ' has-error' : '' }}">
                            <label for="horario" class="control-label">
                              Horario
                            </label>
                                <textarea class="form-control large" style="height:150px;" placeholder="Lunes: 8:00AM - 8:00 PM" name="horario" required autofocus>{{$sucursal->horario}}</textarea>

                                @if ($errors->has('horario'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('horario') }}</strong>
                                    </span>
                                @endif
                        </div>
                      </div>
                      <div class="col-12 col-md-12 col-lg-6">
                        <div class="{{ $errors->has('iframe') ? ' has-error' : '' }}">
                            <label for="iframe" class="control-label">
                              URL Google Maps
                            </label>
                                <textarea class="form-control large iframe" style="height:150px;" placeholder="http://..." name="iframe" required autofocus>{{$sucursal->iframe}}</textarea>

                                @if ($errors->has('iframe'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('iframe') }}</strong>
                                    </span>
                                @endif
                        </div>
                      </div>
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-12 col-md-12 col-lg-6">
                        <div class="row">
                          <div class="col-12 col-md-12 col-lg-6">
                            <label for="longitud" class="control-label">Longitud</label>
                            <input placeholder="Longitud" disabled value="{{$sucursal->lng}}" type="text" class="form-control large" name="lng" autofocus>
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                            <label for="latitude" class="control-label">Latitud</label>
                            <input placeholder="Latitud" disabled  value="{{$sucursal->lat}}" type="text" class="form-control large" name="lat" autofocus>
                          </div>
                        </div>
                      </div>
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-12">
                      </br>
                        <label for="visible">{{($sucursal->visible) ? "Visible en " : "No visible en "}} http://{{$_SERVER['HTTP_HOST']}}?</label>
                      </div>
                      <div class="col-12">
                      </br>
                        <label for="domicilio">{{($sucursal->domicilio) ? "Permite entregas a domicilio" : "No permite entregas a domicilio"}}</label>
                      </div>
                    </div>
                    <div class="col–12">
                      <hr>
                      <div class="col-md-3 nopadding">
                        <button type="submit" class="btn btn-primary large">
                        <i class="fa fa-save"></i>    Actualizar
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
                <br>
                <hr>
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                        <h3 class="titulo">Franquiciatarios asociados</h3>
                        <table class="listaf table table-stripped">
                        @if (count($sucursal->franquiciatarios) > 0)
                            @foreach ($sucursal->franquiciatarios as $franq)
                              <tr>
                                @if ($franq->usuario != null)
                                  <td>{{$franq->usuario->name}}</td>
                                @endif
                              </tr>
                            @endforeach
                        @endif
                      </table>
                      </div>
                  </div>
                </div>
                <br>
                <hr>
                <div class="card-body">
                  <h3 class="titulo">Fotos de la sucursal</h3>
                  <div class="row">
                      @if (count($sucursal->imagenes) > 0)
                        @foreach ($sucursal->imagenes as $imagen)
                          <div class="col-2">
                            <div class="card topp">
                              @if ($imagen->imagen == null)
                                <span class="texto">Imagen eliminada</span>
                                <img src="{{asset("images/black.png")}}" class="card-img-top" alt="{{$sucursal->titulo}}">
                                @else
                                  <span class="texto">{{$imagen->imagen->titulo}}</span>
                                  <img src="/imagenes/watcharlittle/{{md5($imagen->imagen->id)}}" class="card-img-top" alt="{{$imagen->titulo}}">
                              @endif
                              <div class="row bg-secondary" style="margin:0;">
                                <div class="col">
                                  <div class="input-group">
                                    <div class="input-group">
                                      <a href="/sucursales/deleteimagen/{{md5($imagen->id)}}" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        @endforeach
                        @else
                          <div class="col">
                            No hay fotos para este sucursal.
                          </div>
                      @endif
                  </div>
                  <br>
                  <a href="#" class="btn btn-link" data-toggle="modal" data-target="#data">
                    <i class="fa fa-plus"></i> Agregar imagenes
                  </a>
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
                <form class="" action="/sucursales/pi?cid={{md5($sucursal->id)}}" method="post">

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
    $(".iframe").on('keyup',function(){
      $(".iframe").val($(".iframe").val().replace('" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen=""></iframe>',""));
      $(".iframe").val($(".iframe").val().replace('<iframe src="',""));
    });
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
      $(".franquiciatario").bind("change",function(){
        $.post("/sucursales/agregarfranquiciatario?cid={{md5($sucursal->id)}}&franq="+$(this).val(),function(data){
          $(".listaf").append($("<tr>").append($("<td>").text(data)).append($("<td class='drop'>").append($("<i class='fa fa-trash'>"))));
          addToast2("Sucursal","Hace 1 segundo","Franquiciatario agregado")
          drop();
        });
      });
      drop();
    });
    var drop = function(){
      $(".drop").bind("click",function(){
        var e = $(this);
        var id = e.attr("id");
        e.empty();
        e.append("<i class='fa fa-gear fa-spin'></i>")
        $.post("/sucursales/quitar?cid="+id,function(data){
          e.parent().remove();
          addToast2("Sucursal","Hace 1 segundo","Franquiciatario eliminado")
        });
      });
    }
  </script>
@endsection
