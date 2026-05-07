@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
  <div class="col">
      <div class="card card-default large">
          <div class="card-body">
            <div class="clearfix">
              <div class="pull-left">
                <h3>Categorías de platillos</h3>
              </div>
              <div class="pull-right">
                <a href="#" data-toggle="modal" data-target="#data" class="btn btn-primary">
                  <i class="fa fa-plus"></i>
                  Nueva categorìa
                </a>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col">
                <ul class="nolist">
                  Opciones:

                  <li>
                    <i class="fa fa-trash"></i>  Eliminar
                  </li>
                </ul>
              </div>
            </div>
            <hr>
            <div class="row">
                @if (count($categorias) > 0)
                  @foreach ($categorias as $categoria)
                    <div class="col-2">
                      <div class="card topp">
                        @if (count($categoria->platillos) == 0)
                            <div class="card-img-top ">
                              <img src="{{asset("images/black.png")}}" class="imagen-fit" alt="">
                            </div>
                          @else
                            @php
                              $t = 0;
                            @endphp
                            @foreach ($categoria->platillos as $p)
                              @if (count($p->imagenes) > 0 && $t== 0)
                                @php
                                  $t = 1;
                                @endphp
                                <img src="/imagenes/watcharlittle/{{md5($p->imagenes[0]->imagen_id)}}" class="imagen-fit">
                              @endif
                            @endforeach
                        @endif
                        <span class="texto">{{str_replace("."," ",$categoria->titulo)}}</span>
                        <span class="texto">{{count($categoria->platillos)}} elementos</span>
                        <div class="row bg-secondary" style="margin:0;">
                          <div class="col">
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <a href="/categorias/delete/{{md5($categoria->id)}}" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                  @else
                    No hay categorías en el sistema, agrega una nueva!.
                @endif
            </div>
          </div>
        </div>
      </div>
</div>
<div class="modal fade" id="data" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Nueva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" autocomplete="off" action="/categorias/guardar">
          <div class="form-group">
            <label for="exampleInputEmail1">Título</label>
            <input type="text" autofocus name="titulo" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Ingresa el título">

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
