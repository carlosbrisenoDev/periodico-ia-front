@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Alumnos</h5>
          <h6 class="card-subtitle mb-2 text-muted">Factuación</h6>
          <hr>
            <table class="table table-sm table-striped table-hover" id="facturasventas">
              <thead>
                <td>#</td>
                <td>Nombre</td>
                <td>T&eacute;lefono</td>
                <td>Correo</td>
                <td>Factura</td>
                <td>Razón Social</td>
                <td>RFC</td>
                <td>Correo Fiscal</td>
              </thead>
              <tbody>
                @php
                  $i = 1;
                @endphp
                @foreach (\App\inscripciones::all() as $c)
                  @if (isset($c->cliente->nombre) && !strstr($c->cliente->nombre,"PRUEBA") && $c->cliente->status == 4 && $c->factura == "Si")
                    <tr>
                      <td>{{$i++}}</td>
                      @php
                        $cl = $c->cliente;
                      @endphp
                      <td>{{$c->nombre_completo}}</td>
                      <td>{{$c->tel}}</td>
                      <td>
                        <a href="/bandeja/nuevo/enviar?a={{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                          {{$c->correo}}
                        </a>
                      </td>
                      <td>{{$c->factura}}</td>
                      <td>{{$c->razon_social}}</td>
                      <td>{{$c->rfc}}</td>
                      <td>{{$c->correo_fiscal}}</td>
                    </tr>
                  @endif
                @endforeach
              </tbody>
            </table>
            <hr>
            <a target="_blank" class="btn btn-success" href="/clientes/facturas/get">
              <i class="fa fa-download"></i>
            </a>
          </div>
        </div>
    </div>
    <div class="col-12 col-md-12 col-lg-6">

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
  $('#facturasventas').DataTable();
</script>
@endsection
