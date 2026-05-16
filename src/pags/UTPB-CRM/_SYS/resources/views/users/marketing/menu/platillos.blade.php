@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
  <div class="col">
      <div class="card card-default large">
          <div class="card-body">
            <div class="clearfix">
              <div class="pull-left">
                <h3>Platillos</h3>
              </div>
              <div class="pull-right">
                <a href="#" data-toggle="modal" data-target="#data" class="btn btn-primary">
                  <i class="fa fa-plus"></i>
                  Nuevo
                </a>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col">
                <ul class="nolist">
                  Opciones:
                  <li>
                    <i class="fa fa-image"></i>  Gestionar imagenes
                  </li>
                  <li>
                    <i class="fa fa-trash"></i>  Eliminar platillo
                  </li>
                </ul>
              </div>
            </div>
            <hr>
                @foreach (\App\categoria::all() as $categoria)
                  <h4>
                    {{$categoria->titulo}}
                  </h4>
                  <div class="row">
                  @if (count($categoria->platillos) > 0)
                    @foreach ($categoria->platillos as $platillo)
                      <div class="col-2">
                        <div class="card">
                          @if (count($platillo->imagenes) == 0)
                              <div class="card-img-top">
                                <img src="{{asset("images/black.png")}}" class="imagen-fit" alt="{{$platillo->titulo}}">
                              </div>
                            @else
                              <img src="/imagenes/watcharlittle/{{md5($platillo->imagenes[0]->imagen_id)}}" class="imagen-fit" alt="{{$platillo->titulo}}">
                          @endif
                          <span class="text-center">
                            <b>{{str_replace("."," ",$platillo->nombre)}}</b></br>
                            Visible: {{($platillo->visible == 1) ? "Si" : "No"}}</br>
                            Envio: {{($platillo->envio == 1) ? "Si" : "No"}}</br>
                          </span>
                          <div class="row bg-secondary" style="margin:0;">
                            <div class="col">
                              <div class="input-group">
                                <div class="input-group">
                                  <a href="/platillos/delete/{{md5($platillo->id)}}" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                                  <a href="/platillos/imagenes/{{md5($platillo->id)}}" class="btn btn-info"><i class="fa fa-image"></i></a>
                                  <a href="/platillos/edit/{{md5($platillo->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                      </div>
                    @endforeach
                    @else
                      <div class="col">
                        No hay platillos en esta categoria
                      </div>
                  @endif
                </div>
                <hr>
                @endforeach
          </div>
        </div>
      </div>
</div>
<div class="modal fade" id="data" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Nuevo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" autocomplete="off" action="/platillos/guardar">
          <div class="form-group">
            <label for="exampleInputEmail1">Nombre del platillo</label>
            <input type="text" autofocus name="nombre" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Nombre del platillo">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">Descripción</label>
            <textarea name="descripcion" class="form-control"  placeholder="Descripción"></textarea>
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">Precio</label>
            <input name="precio" class="form-control" required placeholder="0.00" />
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">Categoría</label>
            <select class="form-control" name="categoria_id">
              @foreach (\App\categoria::all() as $cat)
                <option value="{{$cat->id}}">{{$cat->titulo}}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="send hidden"></button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" onclick="$('.send').click();" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')

@endsection
