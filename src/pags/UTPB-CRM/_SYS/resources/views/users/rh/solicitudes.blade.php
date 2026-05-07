@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-header">
        <h4>Solicitudes de empleo</h4>
      </div>
      <div class="card-body">
        <div class="row">
          @foreach (\App\empleado::where("status",3)->get() as $i => $empleado)
            <div class="col-2">
              <div class="card topp">
                  <div class="seleccionar">
                    <i class="fa fa-user"></i>
                  </div>
                  <span class="texto">{{$empleado->nombre}} </span>
                  <span class="texto">{{$empleado->puesto}} </span>
                  <div class="clearfix">
                    <a href="/empleados/empleado/{{md5($empleado->id)}}" class="btn btn-primary large" id="titulo">
                    <i class="fas fa-user-check"></i>    Revisar
                  </a>
                  </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
