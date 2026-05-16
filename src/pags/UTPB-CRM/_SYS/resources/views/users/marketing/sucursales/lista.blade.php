@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">

                    <div class="row">
                      <div class="col-12">
                        <div class="clearfix">
                          <div class="pull-left">
                            <h3>Sucursales</h3>
                          </div>
                          <div class="pull-right">
                            <a href="#" class="btn btn-primary new">
                              <i class="fa fa-plus"></i> Nueva
                            </a>
                          </div>
                        </div>
                        <hr>
                        <div class="row">
                          <ul>
                            <li><i class="far fa-eye-slash"></i> Sucursal no visible en el sitio web</li>
                            <li><i class="far fa-eye"></i> Sucursal visible en el sitio web</li>
                            <li><b>(Suc)</b> Alias</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <div class="row">
                      @foreach ($sucursales as $suc)
                        <div class="col-2">
                          <a class="no-decoration primary" href="/sucursales/sucursal/{{md5($suc->id)}}">
                            <div class="card border-light text-center" style="width: 18rem;">
                              <i class="fas fa-store-alt fa-5x"></i>
                              <div class="card-body">
                                <p class="card-text">
                                  {{$suc->nombre}} ({{$suc->estado}})
                                  ({{$suc->alias}})
                                </p>
                                <p>
                                  @if ($suc->visible == 1)
                                    <i class="far fa-eye"></i>
                                    @else
                                    <i class="far fa-eye-slash"></i>
                                  @endif
                                </p>
                              </div>
                            </div>
                          </a>
                        </div>
                      @endforeach
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('pop')
  <div class="modal fade" id="data" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Nueva sucursal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" autocomplete="off" action="/sucursales/guardar">
            <div class="form-group">
              <label for="exampleInputEmail1">Nombre de la sucursal</label>
              <input type="text" autofocus name="nombre" required class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Nombre">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Franquiciatario</label>
              <select class="form-control" name="usuario_id">
                <option value="0">Sin asignar (Por defecto)</option>
                @foreach (\App\user::all() as $cat)
                  <option value="{{$cat->id}}">{{$cat->name}} ({{$cat->email}})</option>
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
  <script type="text/javascript">
    $(".new").bind("click",function(){
      $(".modal").modal();
    });
  </script>
@endsection
