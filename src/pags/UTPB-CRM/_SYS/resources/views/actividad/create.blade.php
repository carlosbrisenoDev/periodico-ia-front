@extends('users.'.Auth::user()->level->alias.'.home')
@section('styles')
<link rel="stylesheet" href="{{asset("/css/actividades.css")}}?r=3">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
<style>
    .weekDays-selector input {
    display: none!important;
    }

    .weekDays-selector input[type=checkbox] + label {
    display: inline-block;
    border-radius: 6px;
    background: #dddddd;
    height: 40px;
    width: 30px;
    margin-right: 3px;
    line-height: 40px;
    text-align: center;
    cursor: pointer;
    }

    .weekDays-selector input[type=checkbox]:checked + label {
    background: #344767;
    color: #ffffff;
    }
</style>
@endsection
@section('content')
<h2>Crear Registro de Actividades</h2>
<hr>
<div class="card">
    <form action="{{url('/actividades/register')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="card-body">
                <h6>Selecciona un cliente</h6>
                <select class="form-control clientSelect" id="area"  name="cliente">
                    <option>Selecciona una opción</option>
                    @foreach(\App\cliente::get() as $cliente)
                    <option value="{{ md5($cliente->id) }}">
                        {{ $cliente->full_name() }} 
                    </option>
                    @endforeach
                </select>
                <hr>
                <h6>¿En que fecha realizaste la actividad?</h6>
                {{-- <input id="selectDate" class="form-control" name="" type="date" required/> --}}
                <input type="text" class="form-control" name="fechaRealizada" />
                <h6>Del <span id="date-s-picked"></span> al <span id="date-e-picked"></span></h6>
                <hr>
                <h6>¿Via de Comunicación?</h6>
                {{-- <input id="selectDate" class="form-control" name="" type="date" required/> --}}
                <select class="form-control" name="via_comunicacion" id="">
                    <option value="Ninguna">Ninguna</option>
                    <option value="Telefono">Telefono</option>
                    <option value="WhatsApp">WhatsApp</option>
                    <option value="Facebook">Facebook</option>
                    <option value="Instagram">Instagram</option>
                    <option value="Correo">Correo</option>
                    <option value="Pagina Web">Pagina Web</option>
                    <option value="Mensaje de Texto">Mensaje de Texto</option>
                    <option value="Presencial">Presencial</option>
                    <option value="Otro">Otro</option>
                </select>
                <hr>
                <h6>¿Que dias usaste para realizar la actividad?</h6>
                <div class="weekDays-selector">
                    <input type="checkbox" id="weekday-mon" class="weekday" name="weekday[]"  value="1"/>
                    <label for="weekday-mon">L</label>
                    <input type="checkbox" id="weekday-tue" class="weekday" name="weekday[]"  value="2"/>
                    <label for="weekday-tue">M</label>
                    <input type="checkbox" id="weekday-wed" class="weekday" name="weekday[]"  value="3"/>
                    <label for="weekday-wed">M</label>
                    <input type="checkbox" id="weekday-thu" class="weekday" name="weekday[]"  value="4"/>
                    <label for="weekday-thu">J</label>
                    <input type="checkbox" id="weekday-fri" class="weekday" name="weekday[]"  value="5"/>
                    <label for="weekday-fri">V</label>
                </div>
                <h6>¿Que tipo de actividad realizaste?</h6>
                <hr>
                <h6>Buscar actividad por Nombre</h6>
                <input type="text" class="form-control searchAct" id="searchAct">
                <div class="row">                
                    @foreach(\App\catalogo_actividades::get() as $act)
                        @if($act->tipo_actividad==null || ($act->tipo_actividad==1 && $act->usuario_id==auth()->user()->id))
                            <div class="col-12 divAct" style="text-align: center;" data-name-act="{{$act->titulo}}" id="{{md5($act->id)}}">
                                <label for="radio-card-{{md5($act->id)}}" class="radio-card" style="text-align: left;width: 100%;">
                                    <input type="radio" name="actividad" class="actsel" id="radio-card-{{md5($act->id)}}" value="{{md5($act->id)}}" required data-timetodo="{{$act->tiempo_promedio}}"/>
                                    <div class="card-content-wrapper">
                                    <span class="check-icon"></span>
                                    <div class="card-content">
                                        <h4>{{$act->titulo}}</h4>
                                        @if($act->pasos)
                                        <ol type="1">
                                            @foreach(json_decode($act->pasos) as $paso)
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
                        @endif
                    @endforeach
                    @if(count(\App\catalogo_actividades::get())<=0)
                    <h3 class="text-danger mt-2 mb-2"> Lo siento no puedes continuar registrando en el formulario si no hay actividades registradas.</h3>
                    @endif
                </div>
                <hr>
                <h6>¿Cuanto Tiempo te tomo realizar la actividad? <small>(En minutos)</small></h6>
                <input type="number" name="time" class="form-control" id="timetodo">
                <hr>
                <h6>¿Comentario sobre esta actividad?</h6>
                <textarea name="comment" style="width: 100%;" class="form-control"></textarea>
                <hr>
                <div style="text-align: end;">
                    <button type="submit" class="btn btn-success" <?php if(count(\App\catalogo_actividades::get())<=0){echo('disabled');}?>>Registrar</button>
                </div>            
            </div>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script>
    $(document).ready(function() {
        const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        const dias_semana = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $('.clientSelect').select2();
        $("input[name='fechaRealizada']").daterangepicker(
            {},
            function (start, end, label) {
            let startDate = start.format("YYYY-MM-DD").toString();
            let endDate = end.format("YYYY-MM-DD").toString();
            const fechas = new Date(start);
            const fechae = new Date(end);
            var fs = dias_semana[fechas.getDay()] + ', ' + fechas.getDate() + ' de ' + meses[fechas.getMonth()] + ' de ' + fechas.getUTCFullYear();
            var fe = dias_semana[fechae.getDay()] + ', ' + fechae.getDate() + ' de ' + meses[fechae.getMonth()] + ' de ' + fechae.getUTCFullYear();
            document.getElementById("date-e-picked").innerHTML = fe;
            document.getElementById("date-s-picked").innerHTML = fs;
            
            }
        );
    });
    $(document).on('click','.actsel', async function(event) {
        $('#timetodo').val($(this).attr('data-timetodo'));
    });

    $(document).on('keyup','.searchAct', async function(event) {
        // $("div").find(`[data-name-act='${$(this).val()}']`).
        // const visible = document.querySelectorAll(`.matcontainer > div > .${$(this).val()}`);
        const all = document.querySelectorAll(`.divAct`);
        var data = $(this).val();
        
        all.forEach(e => {
            var el = $(`#${e.id}`)
            var name = el.attr('data-name-act');
            name = name.toUpperCase();
            console.log(name)
            if(name.includes(data.toUpperCase())){
                el.css('display','inline-block')
            }
            else{
                el.css('display','none')
            }        
      })
    });
    

</script>
@endsection