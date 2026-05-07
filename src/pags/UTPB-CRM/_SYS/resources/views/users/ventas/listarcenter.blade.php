@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">CallCenter</h5>
          <h6 class="card-subtitle mb-2 text-muted">Clientes</h6>
          <hr>
            <table class="table table-sm table-striped table-hover">
              <thead>
                <td>#</td>
                <td></td>
                <td>Nombre</td>
                <td>Paterno</td>
                <td>Materno</td>
                <td>T&eacute;lefono</td>
                <td>Correo</td>
              </thead>
              <tbody>
                @foreach (\App\cliente::where("status",">=",4)->get() as $c)
                  <tr>
                    <td style="text-align:center;">
                      <a href="/ventas/rollcenter?c={{$c->id}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para ver">
                        UOV-{{\Carbon\Carbon::parse($c->created_at)->format("Y")}}-{{$c->id}}
                      </a>
                    </td>
                    <td>
                      @if (count($c->encuestas) > 0)
                        @foreach ($c->encuestas as $en)
                          @if ($en->respondida == 0)
                              <i class="fa fa-question text-warning"></i>
                            @else
                              <i class="fa fa-check text-success"></i>
                          @endif
                        @endforeach
                      @endif
                    </td>
                    <td>{{$c->nombre}}</td>
                    <td>{{$c->apat}}</td>
                    <td>{{$c->amat}}</td>
                    <td>{{$c->telefono}}</td>
                    <td>
                      <a href="/bandeja/nuevo/enviar?a={{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                        {{$c->correo}}
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
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
