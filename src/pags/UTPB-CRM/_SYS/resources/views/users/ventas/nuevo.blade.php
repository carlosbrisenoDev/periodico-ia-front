@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">

    <div class="col-12 col-md-6 col-lg-6 col-md-6">
      <img src="{{asset("images/banner2.jpeg")}}?a=1" class="img-fluid">
    </div>

    <div class="col-12 col-md-6 col-lg-6 col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Nuevo</h5>
          <h6 class="card-subtitle mb-2 text-muted">Cliente</h6>
          <hr>
          <form class="" action="/ventas/nuevo" method="post" id="form-client">
            <div class="row">
              <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                <label for="formGroupExampleInput" class="form-label">Nombre(s)</label>
                <input type="text" class="form-control" name="nombre" required id="formGroupExampleInput" placeholder="Nombre(s)">
              </div>
              <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                <label for="formGroupExampleInput2" class="form-label">Apellido Paterno</label>
                <input type="text" class="form-control" name="apat" id="formGroupExampleInput2" placeholder="Apellido Paterno">
              </div>
              <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                <label for="formGroupExampleInput2" class="form-label">Apellido Materno</label>
                <input type="text" class="form-control" name="amat" id="formGroupExampleInput2" placeholder="Apellido Materno">
              </div>
              <div class="ccol-12 col-md-6 col-lg-6 col-xl-6">
                <label for="formGroupExampleInput" class="form-label">Correo electr&oacute;nico</label>
                <input type="text" class="form-control" name="correo" id="formGroupExampleInput" placeholder="alguien@ejemplo.com">
              </div>
              <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                <label for="formGroupExampleInput2" class="form-label">N&uacute;mero de contacto</label>
                <input type="text" class="form-control" name="telefono"  id="formGroupExampleInput2" placeholder="999-99-99-999">
              </div>
              <div class="col-12">
                <label for="formGroupExampleInput2" class="form-label">Antecedentes:</label>
                <textarea class="form-control" required name="antecedente" style="width:100%;height:200px;" placeholder="¿De donde se obtuvo el lead?, problemas, puntos de cuidado."></textarea>
              </div>
              <div class="col-12">
                <label for="formGroupExampleInput2" class="form-label">Escuela:</label>
                <select name="tag" id="" required class="form-control">
                  @foreach(\App\tag::get() as $tag)
                    <option value="{{$tag->id}}">{{ $tag->tag }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12">
                @php
                  $lista = \App\level::select("id")->where('name','Ventas')->orWhere("name","Control escolar");
                @endphp
                <label for=""  class="form-label">Asignado a:</label>
                <select class="form-control" name="agente_id">
                  <option>Seleccionar</option>
                  @foreach (\App\User::whereIn('level_id',$lista->get()->toArray())->get() as $key => $value)
                    <option {{(auth()->user()->id == $value->id) ? "selected" : ""}} value="{{$value->id}}">{{$value->name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <br>
            <div class="row">
              <hr>
              <div class="col">
                <button type="submit" class="btn btn-primary btn-send">
                  Guardar Cliente
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    
  </div>
@endsection
@section('styles')
  <style media="screen">
    hr{
      height:10px;
      background-color:#f6f6f6;
      border:0;
    }
    .text-muted{
      color:#BD773E !important;
    }
  </style>
@endsection
@section('scripts')
<script>
  var clickLock = 0;
  $(document).on('click','.btn-send', async function(event) {
    if (clickLock == 0) {
        clickLock = 1;
        $(this).addClass('disabled');
        $(this).attr('disabled','disabled');
        $('#form-client').submit();
        setTimeout(function(){  
           clickLock = 0;
           $(this).removeClass('disabled');
           $(this).removeAttr('disabled','disabled');
        }, 4000);
    }
  });
</script>
@endsection
