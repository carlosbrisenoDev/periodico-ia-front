@extends('layouts.website')
@section('content')
  <div class="heading3">
    <h2>
      Registro de empleado
    </h2>
  </div>
  <div class="jumbotron">
    Hola, {{$empleado->nombre}}, para ser parte de Shirushi y entablar comunicación con uno de nuestros representantes del corporativo, completa la información que nos haz proporcionado; al finalizar, recibirá un correo electrónico.
  </div>

  <form class="" action="/empleados/anexarsolicitud" method="post">
    <input type="hidden" name="cid" value="{{md5($empleado->id)}}">
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">Nombre completo</label>
      <input type="text" class="form-control"  disabled id="inputEmail4" value="{{$empleado->nombre}}">
    </div>
    <div class="form-group col-md-6">
      <label for="inputEmail4">Correo electrónico</label>
      <input type="text" class="form-control"  disabled id="inputEmail4" value="{{$empleado->correo}}">
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">Edad</label>
      <input type="text" class="form-control"  name="edad" placeholder="18 años" id="inputEmail4" value="">
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">Estado civil</label>
      <input type="text" class="form-control"  name="estadocivil" placeholder="Estado civil" id="inputEmail4" value="">
    </div>
    <div class="form-group col-md-6">
      <label for="inputEmail4">Hijos</label>
      <input type="text" class="form-control"  name="hijos" placeholder="Si, 4 ó No, sin hijos" id="inputEmail4" value="">
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">Puesto(s) solicitado</label>
      <input type="text" class="form-control"  name="puesto" id="inputEmail4" placeholder="Gerencia, Repartidor, Chef, Caja, ..." value="">
    </div>
    <div class="form-group col-md-6">
      <label for="inputEmail4">Experiencia en el puesto(s) solicitado</label>
      <input type="text" class="form-control" name="experiencia" placeholder="Cocina 3 años, repartidos 5 años, ..." id="inputEmail4" value="">
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">Nivel de estudios</label>
      <input type="text" class="form-control"  name="nivelestudios" id="inputEmail4" placeholder="Nivel de estudios: Primaría, Universidad, ..." value="">
    </div>
    <div class="form-group col-md-6">
      <label for="inputEmail4">Especidalidad</label>
      <input type="text" class="form-control" name="especialidad" placeholder="Cocina, Sistemas, Servicio al cliente, ..." id="inputEmail4" value="">
    </div>
  </div>
  <div class="form-group">
    <label for="inputAddress">Dirección</label>
    <input type="text" class="form-control"   name="direccion" id="inputAddress" value="{{$empleado->direccion}}" placeholder="Dirección">
  </div>
  <div class="form-group">
    <label for="inputAddress2">Teléfono</label>
    <input type="text" class="form-control" id="inputAddress2" name="telefono" value="{{$empleado->telefono}}" placeholder="(555) 228 00 00)">
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputCity">Ciudad</label>
      <input type="text" class="form-control" disabled placeholder="Ciudad" id="inputCity">
    </div>
    <div class="form-group col-md-6">
      <label for="inputCity">Estado de residencia</label>
      <input type="text" class="form-control" name="estado" placeholder="Estado" id="inputCity">
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputCity">Nacionalidad</label>
      <input type="text" class="form-control" name="nacionalidad" placeholder="Nacionalidad" id="inputCity">
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputCity">¿Cómo nos conoció?</label>
      <textarea class="form-control" name="como" placeholder="Respuesta" id="inputCity"></textarea>
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputCity">¿Por qué quiere trabajar con nosotros?</label>
      <textarea class="form-control" name="porque" placeholder="Respuesta" id="inputCity"></textarea>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Guardar información</button>
</form>
@endsection
