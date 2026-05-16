@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
  <div class="col">
      <div class="card card-default large">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <h3>
                  <a href="/platillos/lista/ver">
                    Platillos
                  </a>
                </h3>
                <h4 class="titulo">{{$platillo->nombre}}</h4>
                <h5>{{($platillo->categoria != null) ? $platillo->categoria->titulo : "Sin categoria"}}</h5>
              </div>
            </div>
            <hr>
            <form class="form-horizontal" method="POST" autocomplete="off" action="/platillos/actualizar">
              <input type="hidden" name="cid" value="{{md5($platillo->id)}}">
              <div class="form-group">
                <label for="exampleInputEmail1">Nombre del platillo</label>
                <input type="text" autofocus name="nombre" value="{{$platillo->nombre}}" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Nombre del platillo">
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Descripción</label>
                <textarea name="descripcion" value="{{$platillo->descripcion}}" class="form-control" placeholder="Descripción">{{$platillo->descripcion}}</textarea>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Precio</label>
                <input name="precio" value="{{$platillo->precio}}" class="form-control" required placeholder="0.00" />
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Categoría</label>
                <select class="form-control" name="categoria_id">
                  @foreach (\App\categoria::all() as $cat)
                    <option {{($platillo->categoria_id == $cat->id) ? "selected" : ""}} value="{{$cat->id}}">{{$cat->titulo}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <input type="checkbox" {{($platillo->visible) ? "checked" : ""}} name="visible" value="1" id="visible">
                <label for="visible">¿Hacer visible en http://{{$_SERVER['HTTP_HOST']}}/w/menu?</label>
              </div>
              <div class="form-group">
                <input type="checkbox" {{($platillo->envio) ? "checked" : ""}} name="envio" value="1" id="envio">
                <label for="envio">¿Desactivar el envio a domicilio en http://{{$_SERVER['HTTP_HOST']}}/w/menu?</label>
              </div>
              <button type="submit" class="send hidden"></button>
            </form>
            <button type="button" onclick="$('.send').click();" class="btn btn-primary">
              <i class="fa fa-refresh"></i> Actualizar
            </button>
        </div>
      </div>
</div>
@endsection
@section('scripts')

@endsection
