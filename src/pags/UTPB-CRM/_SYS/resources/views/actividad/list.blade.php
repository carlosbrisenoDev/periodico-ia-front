@extends('users.'.Auth::user()->level->alias.'.home')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
@endsection
@section('content')
@php
    $semana = array(
      "Lunes",
      "Martes",
      "Miercoles",
      "Jueves",
      "Viernes"
    );
@endphp
<div class="col-md-12">
    <div class="card card-default">
        <div class="card-body">
          <div class="card-header">
            <div class="row">
              <h6>Filtro de Fechas</h6>
              <div class="col-8">
                <label for="fs">Elije un Rango de Fechas</label>
                <input type="text" name="date_range" required id="dr" class="form-control">
              </div>
              {{-- <div class="col-4">
                <label for="fe">Fecha Fin</label>
                <input type="date" name="fecha_end" required  id="fe" class="form-control">
              </div> --}}
              <div class="col-4" style="align-self: end;">
                <button class="btn btn-sm btn-info btn-find-dates" style="margin:0;">Aplicar Filtros</button>
              </div>
            </div>
            
            
          </div>
            <h4>Actividades Realizadas <span id="range-date"></span></h4>
            <div id="tablesContent">
              @if (count($acts) > 0)
                <table class="table table-stripped" id="acts">
                  <thead>
                    <tr>
                      <th class="text-dark">Actividad Realizada</th>
                      <th class="text-dark">Cliente Atendido</th>
                      <th class="text-dark">Comentarios</th>
                      <th class="text-dark">Tiempo Utilizado</th>
                      
                      <th class="text-dark">Fecha Inicio</th>
                      <th class="text-dark">Fecha Fin</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($acts as $a)
                      @if($a->catalogo_actividades)
                        <tr> 
                          <td><a href="/actividad/info/{{md5($a->id)}}">{{$a->catalogo_actividades->titulo}}</a></td>
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
                          <td>
                            @php
                            $hour = $a->tiempo_tomado;
                            
                            if($a->tiempo_tomado<60){
                              $hour = $a->tiempo_tomado.' minutos';
                            }
                            else{
                              $hour = round($a->tiempo_tomado/60 ,2).' Horas';
                            }    
                            @endphp
                            {{$hour}}
                          </td>
                          
                          {{-- <td>
                            @if(json_decode($a->dias_trabajados))
                            @foreach(json_decode($a->dias_trabajados) as $day)                            
                              {{$semana[$day]}},
                            @endforeach
                            @else
                              Sin Datos
                            @endif
                          </td> --}}
                          <td>{{App\Helper\Helper::fechaEs($a->fecha_inicio) ?? 'No data'}}</td>
                          <td>{{App\Helper\Helper::fechaEs($a->fecha_fin) ?? 'No data'}}</td>
                        </tr>
                      @endif
                    @endforeach
                  </tbody>
                </table>
                <hr>
                <h4>Filtro por Fechas</h4>
                <hr>
                <table class="table table-stripped">
                  <thead>
                    <tr>
                      <td>Actividades Realizadas</td>
                      <td>Tiempo Utilizado</td>
                      <td>Ultima Actividad Realizada</td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>{{auth()->user()->actividadesRealizadas()}}</td>
                      <td>
                        @php
                          $min = auth()->user()->tiempoActividades();
                          $hour = $min;
                          
                          if($hour<60){
                            $hour = $min.' minutos';
                          }
                          else{
                            $hour = round($min/60 ,2).' Horas';
                          }    
                          @endphp
                          {{$hour}}
                      </td>
                      <td>{{auth()->user()->lastActivity()}}</td>
                    </tr>
                  </tbody>
                </table>
                @else
                  <h4>No hay resultados</h4>
              @endif
            </div>
              
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script>
  var table = $('#acts').DataTable();
  let dateQuery = null;
  $(document).ready(function() {
    const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        const dias_semana = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $("input[name='date_range']").daterangepicker(
            {},
            function (start, end, label) {
            let startDate = start.format("YYYY-MM-DD").toString();
            let endDate = end.format("YYYY-MM-DD").toString();
            const fechas = new Date(start);
            const fechae = new Date(end);
            var fs = dias_semana[fechas.getDay()] + ', ' + fechas.getDate() + ' de ' + meses[fechas.getMonth()] + ' de ' + fechas.getUTCFullYear();
            var fe = dias_semana[fechae.getDay()] + ', ' + fechae.getDate() + ' de ' + meses[fechae.getMonth()] + ' de ' + fechae.getUTCFullYear();
            document.getElementById("range-date").innerHTML = 'Del: '+fe+' al '+fs;
            // document.getElementById("date-s-picked").innerHTML = fs;
            
            }
        );
  });
  $(document).on('click','.btn-find-dates', async function(e) {
    var dr = document.getElementById("dr");
    var content = '';
    var content2 = '';
    // var ed = document.getElementById("fe");
    if(dr.value.length < 1 ){
      alert("Elije una fecha");
      e.preventDefault();
      return false
    }
    dateQuery = await serverResponse({_token:'{{csrf_token()}}',rangeDate:dr.value},'/api/actividades/getByDate');
    dateGQuery = await serverResponse({_token:'{{csrf_token()}}',rangeDate:dr.value},'/api/actividades/getByDate/general');
    $('#tablesContent').html('');
    
    if(dateQuery.length!=''){
      dateQuery.forEach(async function(element){        
          content += `<tr>
            <td><a href="/actividad/info/${element.id}">${element.name}</a></td>
            <td>${element.client}</td>
            <td>${element.coment}</td>
            <td>${element.time}</td>
            <td>${element.dateS}</td>
            <td>${element.dateE}</td>
            </tr>`;
      });
      $('#tablesContent').append(`<table class="table table-stripped" id="acts">
        <thead>
          <tr>
            <th class="text-dark">Actividad Realizada</th>
            <th class="text-dark">Cliente Atendido</th>
            <th class="text-dark">Comentarios</th>
            <th class="text-dark">Tiempo Utilizado</th>
            
            <th class="text-dark">Fecha Inicio</th>
            <th class="text-dark">Fecha Fin</th>
          </tr>
        </thead>
        <tbody>
          ${content}
        </tbody>
      </table>
      <hr>
      <h4>Filtro por Fechas</h4>
      <hr>
      <table class="table table-stripped">
        <thead>
          <tr>
            <td>Actividades Realizadas</td>
            <td>Tiempo Utilizado</td>
            <td>Ultima Actividad Realizada</td>
          </tr>
        </thead>
        <tbody id="tcontent2">
        </tbody>
      </table>
      `);
      // console.log(dateQuery)  
    }
    if(dateGQuery.length!=''){
      dateGQuery.forEach(async function(element){        
          content2 += `<tr>
            <td>${element.actividades_realizadas}</td>
            <td>${element.tiempo_usado}</td>
            <td>${element.last_actividad}</td>
            </tr>`;
      });
      $('#tcontent2').html(content2);
    }
  });

  async function serverResponse(param={},url) {
      const result = await $.ajax({
        url: url,
        type: 'POST',
        data: param,
      })
      return result
  }
</script>
@endsection