@extends('users.'.Auth::user()->level->alias.'.home')
@section('styles')
@endsection
@section('content')

<div class="col-md-12">
    <div class="card card-default">
        <div class="card-body">
            <h4>Actividades Realizadas</h4>
              {{-- @if (count($acts) > 0) --}}
                <table class="table table-stripped" id="acts">
                    <thead>
                        <tr>
                            <th class="text-dark">Usuario</th>
                            <th class="text-dark">Actividades Realizadas</th>
                            <th class="text-dark">Tiempo tomado</th>
                            <th class="text-dark">Ultima Fecha Realización</th>
                          </tr>
                    </thead>                    
                    <tbody>
                        @foreach ($users as $u)
                            <tr> 
                                <td><a href="{{url('/actividad/user/info/'.md5($u->id))}}">{{$u->name}}</a> </td>
                                <td>{{$u->actividadesRealizadas()}}</td>
                                <td>{{$u->tiempoActividades()}}</td>
                                <td>{{$u->lastActivity()}}</td>
                            {{-- <td><a href="/actividad/info/{{md5($a->id)}}">{{$a->catalogo_actividades->titulo}}</a></td>
                            @if($a->cliente)
                                <td>{{$a->cliente->full_name()}}</td>
                            @else
                                <td>Sin Cliente Seleccionado</td>
                            @endif
                            @if($a->comentario)
                                <td>{{$a->comentario}}</td>
                            @else
                                <td>Sin Comentarios</td>
                            @endif
                            <td>{{$a->fecha_realizacion}}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>    
                </table>
                {{-- @else --}}
                  
              {{-- @endif --}}
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $('#acts').DataTable();
</script>
@endsection