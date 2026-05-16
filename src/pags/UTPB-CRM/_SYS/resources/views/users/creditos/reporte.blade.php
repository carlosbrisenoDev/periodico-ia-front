@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  @php
    $i = 0;
  @endphp
  <div class="card">
    <div class="card-body">
      <h3>Reportes</h3>
      <hr>
      <table class="table">
        <thead>
          <td>Matrícula</td>
          <td>Alumno</td>
          <td>Estado</td>
          <td>Concepto</td>
          <td>Materia</td>
          <td>Título</td>
          <td>Plazos</td>
          <td>Total</td>
          <td>Tabla</td>
          <td>Pagos de tabla</td>
          <td>Total pagado</td>
          <td>Número de último pago</td>
          <td>Pagos atrasados</td>
          <td>Materias pagadas</td>
          <td>Total de materias pagadas</td>
          <td>Materias tomadas</td>
          <td>Materias reprobadas</td>
        </thead>
        <tbody>
          @foreach (\App\cartera::all() as $cartera)
            <tr>
              <td>
                {{$cartera->cliente->matricula}}
              </td>
              <td>
                {{$cartera->cliente->nombre." ".$cartera->cliente->apat." ".$cartera->cliente->amat}}
              </td>
              <td>
                {{$cartera->cliente->baja == NULL ? "ACTIVO" : "BAJA"}}
              </td>
              <td>
                {{$cartera->concepto}}
              </td>
              <td>
                {{$cartera->valor_materia}}
              </td>
              <td>
                {{$cartera->valor_titulo}}
              </td>
              <td>
                {{$cartera->plazo}}
              </td>
              <td>
                {{$cartera->total}}
              </td>
              <td>
                {{ ($cartera->tablapagos == NULL) ? "Sin tabla" : (($cartera->tablapagos->status != NULL) ? "Pausada" : "Activa")}}
              </td>
              <td>
                @if ($cartera->tablapagos)
                  {{$cartera->tablapagos->pagos->sum("monto")}}
                  @else
                    Sin tabla
                @endif
              </td>
              <td>
                @if ($cartera->tablapagos)
                  {{$cartera->tablapagos->pagados->last() ?$cartera->tablapagos->pagados->last()->acumulado: 0}}
                  @else
                    Sin tabla
                @endif
              </td>
              <td>
                @if ($cartera->tablapagos)
                  {{$cartera->tablapagos->pagados->last() ?$cartera->tablapagos->pagados->last()->numero: 0}}
                  @else
                    Sin tabla
                @endif
              </td>
              <td>
                @if ($cartera->tablapagos)
                  {{$cartera->tablapagos->nopagados->count()}}
                  @else
                    Sin tabla
                @endif
              </td>
              <td>
                @if ($cartera->tablapagos)
                  {{intval($cartera->tablapagos->pagados->last() ?$cartera->tablapagos->pagados->last()->numero: 0) * 2}}
                  @else
                    Sin tabla
                @endif
              </td>
              <td>
                @if ($cartera->tablapagos)
                  {{intval($cartera->tablapagos->pagados->last() ?$cartera->tablapagos->pagados->last()->numero: 0) * 700}}
                  @else
                    Sin tabla
                @endif
              </td>
              <td>
                @php
                  $url = "https://plataformaunisant.mx/unisant/apiEstudy/externos/alumno/consulta.php?token=4ba07dd78a8a6bc15844adebebffc342&matricula=".$cartera->cliente->matricula;
                  $ch = curl_init();
                   curl_setopt($ch, CURLOPT_URL, $url);
                   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                   curl_setopt($ch, CURLOPT_HEADER, 0);
                   $data = json_decode(curl_exec($ch));
                   curl_close($ch);
                   if ($data != NULL && isset($data->response)) {
                     $cursadas = isset($data->response->materias_cursadas) ? count($data->response->materias_cursadas) : 0;
                     $encurso = isset($data->response->materias_en_curso) ? count($data->response->materias_en_curso) : 0;
                     echo $cursadas+$encurso;
                   }
                @endphp
              </td>
              <td>
                @php
                  $a = 0;
                @endphp
                @if ($data != NULL && isset($data->response) && isset($data->response->materias_cursadas))
                  @foreach ($data->response->materias_cursadas as $materia)
                    @if (intval($materia->calificacion) < 6)
                      @php
                        $a++;
                      @endphp
                    @endif
                  @endforeach
                @endif
                {{$a}}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
