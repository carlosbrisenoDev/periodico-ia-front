@extends('users.'.Auth::user()->level->alias.'.home')
@section('styles')
<link rel="stylesheet" href="{{asset("/css/actividades.css")}}?r=0">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<h2>Crear Registro de Actividades</h2>
<hr>
<div>
    <a class="btn btn-info" href="{{url('actividades/list')}}">Regresar</a>
</div>
<hr>
<div class="card">
    <div class="card-body">
        <div class="card-body">
            <h6>Selecciona un cliente</h6>
            <select class="form-control clientSelect" id="area" required  name="cliente" disabled>
                @if($act->cliente)
                <option value="{{ md5($act->cliente->id) }}">
                    {{ $act->cliente->full_name() }} 
                </option>
                @else
                    <option>Sin Alumno Elegido</option>
                @endif
            </select>
            <hr>
            <h6>¿En que fecha realizaste la actividad?</h6>
                <input id="none" class="form-control" name="fechaRealizada" type="text" disabled value="{{$act->fecha_inicio ?? 'No data'}} - {{$act->fecha_fin  ?? 'No data'}}"/>
                <hr>
                <h6>¿Via de Comunicación?</h6>
                {{-- <input id="selectDate" class="form-control" name="" type="date" required/> --}}
                <select class="form-control" name="via_comunicacion" id="" disabled>
                    <option value="{{$act->via_comunicacion ?? 'Desconocida'}}">{{$act->via_comunicacion ?? 'Desconocida'}}</option>
                </select>
                <hr>
            <h6>¿Cuanto Tiempo te tomo realizar la actividad? <small>(En minutos)</small></h6>
                <input type="number" name="time" class="form-control" value="{{$act->tiempo_tomado}}" disabled>
            <hr>
            <h6>¿Que tipo de actividad realizaste con el cliente?</h6>
            <div class="row">                
                {{-- @foreach(\App\catalogo_actividades::get() as $activ) --}}
                    <div class="col-12" style="text-align: center;">
                        <label for="radio-card-{{md5($act->catalogo_actividades->id)}}" class="radio-card" style="text-align: left;width: 100%;">
                            <input type="radio" name="actividad" id="radio-card-{{md5($act->catalogo_actividades->id)}}" value="{{md5($act->catalogo_actividades->id)}}" disabled checked/>
                            <div class="card-content-wrapper">
                            <span class="check-icon"></span>
                            <div class="card-content">
                                <h4>{{$act->catalogo_actividades->titulo}}</h4>
                                @if($act->catalogo_actividades->pasos)
                                <ol type="1">
                                    @foreach(json_decode($act->catalogo_actividades->pasos) as $paso)
                                        <li>
                                            <small>{{$paso}}</small>
                                        </li>
                                    @endforeach
                                </ol>
                                @else
                                    <h5>No se encuentran pasos disponibles</h5>
                                @endif
                                
                            </div>
                            </div>
                        </label>
                    </div>
                    
                    <!-- /.radio-card -->
                {{-- @endforeach --}}
            </div>
            <hr>        
            <h6>¿Comentario sobre esta actividad?</h6>
            <div class="card">
                <div class="card-body">
                    <div class="card-text">
                        @if($act->comentario)
                            {{$act->comentario}}
                        @else
                            Sin Comentarios
                        @endif
                    </div>
                </div>
            </div>
                
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.clientSelect').select2();
    });
</script>
@endsection