@extends('users.'.Auth::user()->level->alias.'.home')
@section('styles')
@endsection
@section('content')

<div class="col-md-12">
    <div class="card card-default">
        <div class="card-body">
            <h4>Catalogo Actividades</h4>
            <a class="btn btn-success" href="{{url('/actividadesCatalogo/create')}}">Crear Nueva Actividad</a>
              @if (count($acts) > 0)
                <table class="table table-stripped" id="acts">
                  <thead>  
                    <tr>
                      <th class="text-dark">Titulo de Actividad</th>
                      <th class="text-dark">Pasos</th>
                      <th class="text-dark">Tiempo Estimado De Ejecución</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($acts as $a)
                      <tr> 
                        <td style="max-width: 700px;overflow:hidden;text-overflow:ellipsis;">{{$a->titulo}}</td>
                          <td>
                              @foreach(json_decode($a->pasos) as $index => $p)
                                <p style="max-width: 700px;overflow:hidden;text-overflow:ellipsis;">#{{$index+1}} {{$p}}</p> 
                              @endforeach
                          </td>
                        <td>{{$a->tiempo_promedio}} Minutos</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                @else
                  <h4>No hay resultados</h4>
              @endif
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
  $('#acts').DataTable();
</script>
@endsection