@extends('layouts.website')
@section('content')
  <div class="heading3">
    <h2>
      Registro de solicitante de Franquicia
    </h2>
  </div>
  <div class="jumbotron">
    Hola, {{$franq->nombre}}, para ser parte de Shirushi y entablar comunicación con uno de nuestros representantes del corporativo, completa la información que nos haz proporcionado; al finalizar, recibirá un correo electrónico.
  </div>

  <form class="" action="/franquiciatarios/anexarsolicitud" method="post">
    <input type="hidden" name="cid" value="{{md5($franq->id)}}">
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">Nombre completo</label>
      <input type="text" class="form-control"  disabled id="inputEmail4" value="{{$franq->nombre}}">
    </div>
    <div class="form-group col-md-6">
      <label for="inputEmail4">Correo electrónico</label>
      <input type="email" class="form-control"  disabled id="inputEmail4" value="{{$franq->correo}}">
    </div>
  </div>
  <div class="form-group">
    <label for="inputAddress">Dirección</label>
    <input type="text" class="form-control"  name="direccion" id="inputAddress" value="{{$franq->direccion}}" placeholder="Dirección">
  </div>
  <div class="form-group">
    <label for="inputAddress2">Teléfono</label>
    <input type="text" class="form-control" id="inputAddress2" name="telefono" value="{{$franq->telefono}}" placeholder="(555) 228 00 00)">
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputCity">Ciudad</label>
      <input type="text" class="form-control" disabled placeholder="Ciudad" id="inputCity">
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Guardar información</button>
</form>
@endsection
