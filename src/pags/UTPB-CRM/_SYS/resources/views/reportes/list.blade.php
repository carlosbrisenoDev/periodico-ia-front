@extends('users.' . Auth::user()->level->alias . '.home')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>
@endsection
@section('content')
@php
    $reportes = \App\reportes_tickets::where(function($query){
        $query->whereJsonContains('areas',md5(auth()->user()->levels->id))
        ->orwhereJsonContains('areas',"all");
    })
    ->where(function($query){
        $query->whereJsonContains('usuarios',strval(auth()->user()->id))
        ->orwhereJsonContains('usuarios',"all");
    })
    ->orderBy('created_at','DESC')
    ->get();

    $arr_prioridad = array(0 => 'info',1 => 'warning',2 => 'danger');
    $arr_labprioridad = array(0 => 'Baja',1 => 'Media',2 => 'Alta');
    $arr_txtprioridad = array(0 => 'white',1 => 'black',2 => 'white');

    $arr_estadobg = array(0 => 'light',1 => 'info',2 => 'warning',3 => 'success',4 => 'danger');
    $arr_estadocol = array(0 => 'black',1 => 'black',2 => 'black',3 => 'white',4 => 'white');
    $arr_estadolb = array(0 => 'Pendiente',1 => 'Visto',2 => 'En Revisión',3 => 'Finalizado Con Exitó',4 => 'Finalizado Sin Exitó');
    // ->toSql();
    // dd($reportes);
@endphp
<div class=" mt-5">
    <h4>Reportes Recibidos</h4>
    <div class="">
        <table class="table table-responsive table-stripped">
            <thead>
                <tr>
                    <th style="color:#344767;"># Reporte</th>
                    <th style="color:#344767;">Titulo</th>
                    <th style="color:#344767;">De</th>
                    <th style="color:#344767;">Cliente</th>
                    <th style="color:#344767;">Prioridad</th>
                    <th style="color:#344767;">Estado</th>
                </tr>
            </thead>
            @foreach($reportes as $reporte)
            <tbody>
                <tr class="cursor-pointer" onclick="window.location='{{ url('/reporte/'.md5($reporte->id)) }}';">
                    <td>#{{\Carbon\carbon::parse($reporte->created_at)->format('Y')}}{{$reporte->user->id}}{{$reporte->id}}</td>
                    <td>{{$reporte->titulo}}</td>
                    <td>{{$reporte->user->name}}</td>
                    @if($reporte->cliente)
                        <td>{{$reporte->cliente->nombre ?? 'Desconocido'}}</td>
                    @else
                        <td>No hay cliente</td>
                    @endif
                    <td><span class="badge bg-{{$arr_prioridad[$reporte->prioridad]}} text-{{$arr_txtprioridad[$reporte->prioridad]}}">{{$arr_labprioridad[$reporte->prioridad]}}</span></td>
                    <td><span class="badge bg-{{$arr_estadobg[$reporte->estado]}} text-{{$arr_estadocol[$reporte->estado]}}">{{$arr_estadolb[$reporte->estado]}}</span></td>
                    
                </tr>
            </tbody>
            @endforeach
        </table>
            {{-- <div class="card">
                <div class="card-header">
                    <span>
                        <span>Reporte #{{\Carbon\carbon::parse($reporte->created_at)->format('Y')}}{{$reporte->user->id}}{{$reporte->id}}:</span> <span class="h4"> {{$reporte->titulo}}</span>
                        <span class="ml-4" style="margin-left: 25px;">Prioridad: <span class="badge bg-{{$arr_prioridad[$reporte->prioridad]}} text-{{$arr_txtprioridad[$reporte->prioridad]}}">{{$arr_labprioridad[$reporte->prioridad]}}</span></span>
                        <span class="ml-4" style="margin-left: 25px;">Estado: <span class="badge bg-{{$arr_estadobg[$reporte->estado]}} text-{{$arr_estadocol[$reporte->estado]}}">{{$arr_estadolb[$reporte->estado]}}</span></span>
                    </span>
                </div>
                <div class="card-body">
                    <div class="card-text" x-data="{desc:false}">
                        <div x-show="!desc">
                            {!! Illuminate\Support\Str::limit($reporte->descripcion,50) !!}
                            @if(Illuminate\Support\Str::length($reporte->descripcion)>=50)
                                <a class="text-info cursor-pointer" x-on:click="desc=!desc">Leer mas...</a>
                            @endif
                        </div>
                        <div x-show="desc">{!! $reporte->descripcion !!}
                            <a class="text-info cursor-pointer" x-on:click="desc=!desc">Mostrar menos</a>
                        </div>
                    </div> 
                    <div class="card-text">
                        {{ count($reporte->respuestas) }} <i class="fa fa-message"></i>
                    </div>
                    <div class="card-text mt-4" x-data="{response:false}">
                        @if($reporte->estado ==0 || $reporte->estado ==1 || $reporte->estado ==2)
                            <a class="btn btn-light" x-on:click="response=!response">Responder</a>
                            <div class="card" x-show="response" style="position: absolute;z-index:5;">
                                <div class="card-body">
                                    <form action="{{url('/reporte/response')}}" method="POST">
                                        @csrf
                                        <input type="hidden" value="{{md5($reporte->id)}}" name="reporte">
                                        <div class="form-row row">
                                            <div class="form-group col-md-12">
                                                <label for="name">Titulo de Reporte</label>
                                                <input type="text" class="form-control" id="titulo" placeholder="Titulo" required name="titulo" readonly value="Re: {{$reporte->titulo}}">
                                            </div>
                                        </div>
                                        
                                        <div class="form-row row">
                                            <div class="form-group col-md-12">
                                                <label for="precio">Descripción detallada</label>
                                                <textarea class="form-control" id="descripcion{{$reporte->id}}" placeholder="Descripción" required  name="descripcion" rows="4"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="form-row row">
                                            <div class="form-group col-md-12">
                                                <label for="tipoDescuento">Area a donde va redirigido <small>(Si cambias este campo, no volveras a ver este reporte hasta que te lo reasignen.)</small></label>
                                                @php
                                                    $areas = \App\level::get();
                                                @endphp
                                                <select class="form-control areaSelect areaSelect{{$reporte->id}}" id="area{{$reporte->id}}" data-index="{{$reporte->id}}" name="area[]" multiple="multiple" style="width: 100%;">
                                                    <option value="all"0 data-lvl="all">Todas las Areas</option>
                                                    @foreach($areas as $area)
                                                    <option value="{{ md5($area->id) }}" data-lvl="{{ $area->id }}" id="opt{{ $area->id }}">{{ $area->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
    
                                        <div class="form-row row">
                                            <div class="form-group col-md-12">
                                                <label for="tipoDescuento">Usuario en Especifico <small>(Si cambias este campo, no volveras a ver este reporte hasta que te lo reasignen.)</small></label>
                                                <div class="col-12">
                                                    <select class="form-control areaSelectUsers areaSelectUsers{{$reporte->id}}" id="users{{$reporte->id}}" data-index="{{$reporte->id}}" name="users[]" multiple="multiple" disabled style="width: 100%;"></select>
                                                </div>
                                            </div>
                                        </div>
    
                                        <div class="form-row row">
                                            <div class="form-group col-md-12">
                                                <label for="tipoDescuento">Cambiar Prioridad</label>
                                                <select class="form-control " id="tipoDescuento" required  name="prioridad">
                                                    <option value="2">Alta</option>
                                                    <option value="1">Media</option>
                                                    <option value="0">Baja</option>
                                                </select>
                                            </div>
                                        </div>
    
                                        <div class="form-row row">
                                            <div class="form-group col-md-12">
                                                <label for="estado{{$reporte->id}}">Cambiar Estado</label>
                                                <select class="form-control estado" id="estado{{$reporte->id}}" required  name="estado" data-index="{{$reporte->id}}">
                                                    <option value="1">Visto</option>
                                                    <option value="2">En Revisión</option>
                                                    <option value="3">Finalizado con Exitó</option>
                                                    <option value="4">Finalizado sin Exitó</option>
                                                </select>
                                            </div>
                                        </div>
    
                                        <div class="form-row row d-none" id="descFail{{$reporte->id}}">
                                            <div class="form-group col-md-12">
                                                <label for="precio">¿Porqué?</label>
                                                <textarea class="form-control" id="fallo{{$reporte->id}}" placeholder="Descripción de porque no fue exitoso el reporte"  name="fallo" rows="4"></textarea>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                    <h5>Respuestas:</h5>
                    @foreach($reporte->ultimasRespuestas as $respuesta)
                        <div class="card">
                            <div class="card-header">
                                <strong> {!! $respuesta->titulo !!}</strong>
                                <span class="ml-4" style="margin-left: 25px;">Cambio Prioridad a: <span class="badge bg-{{$arr_prioridad[$respuesta->prioridad]}} text-{{$arr_txtprioridad[$respuesta->prioridad]}}">{{$arr_labprioridad[$respuesta->prioridad]}}</span></span>
                                <span class="ml-4" style="margin-left: 25px;">Cambio Estado a: <span class="badge bg-{{$arr_estadobg[$respuesta->estado]}} text-{{$arr_estadocol[$respuesta->estado]}}">{{$arr_estadolb[$respuesta->estado]}}</span></span>
                            </div>
                            <div class="card-body">
                                {!! $respuesta->descripcion !!}
                            </div>
                        </div>
                        <hr>
                    @endforeach
                    @if(count($reporte->respuestas)>3)
                        <a class="btn btn-primary" style="margin-left: 50% !important;" href="{{ url('/reporte/'.md5($reporte->id)) }}">Ver todas las respuestas</a>
                    @endif
                    
                </div>
            </div> --}}
            {{-- <div class="content">
                <div class="card" x-data="{rep:false}">
                    <div class="card-body cursor-pointer" style="padding-bottom: 0;" x-on:click="rep=!rep">
                        <div class="card-body" >
                            <h3>REPORTE</h3>
                            <span>#{{\Carbon\carbon::parse($reporte->created_at)->format('Y')}}{{$reporte->user->id}}{{$reporte->id}}</span>
                        </div>
                    </div>
                    <div class="card-body" x-show="rep">
                        <div class="row">
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-body" >
                                        <div class="mb-4" style="border-radius:15px;border:1px solid gray;padding:10px 25px;max-height:300px;overflow:auto;">
                                            <h5 style="margin: initial;">{{$reporte->titulo}}</h5>
                                        </div>
                                        <div class="mb-4" style="border-radius:15px;border:1px solid gray;padding:5px 20px;max-height:300px;overflow:auto;" x-data="{desc:false}">
                                            <div x-show="!desc">
                                                {!! Illuminate\Support\Str::limit($reporte->descripcion,50) !!}
                                                @if(Illuminate\Support\Str::length($reporte->descripcion)>=50)
                                                    <a class="text-info cursor-pointer" x-on:click="desc=!desc">Leer mas...</a>
                                                @endif
                                            </div>
                                            <div x-show="desc">{!! $reporte->descripcion !!}
                                                <a class="text-info cursor-pointer" x-on:click="desc=!desc">Mostrar menos</a>
                                            </div>
                                        </div>
            
                                        <div class="mt-4 bg-{{$arr_estadobg[$reporte->estado]}} text-{{$arr_estadocol[$reporte->estado]}}" style="border-radius:15px;none;padding:5px 20px;max-height:300px;overflow:auto;">
                                            Estado: <strong>{{$arr_estadolb[$reporte->estado]}}</strong>
                                        </div>
                                        <div class="mt-1 bg-dark text-white" style="border-radius:15px;none;padding:5px 20px;max-height:300px;overflow:auto;">
                                            Usuario: <strong>{{$reporte->user->name}}</strong>
                                        </div>
                                        <div class="mt-1 bg-dark text-white" style="border-radius:15px;none;padding:5px 20px;max-height:300px;overflow:auto;">
                                            Departamento: <strong>{{$reporte->user->levels->name}}</strong>
                                        </div>
                                        <div class="mt-1 bg-{{$arr_prioridad[$reporte->prioridad]}} text-{{$arr_txtprioridad[$reporte->prioridad]}}" style="border-radius:15px;none;padding:5px 20px;max-height:300px;overflow:auto;">
                                            Priodidad: <strong>{{$arr_labprioridad[$reporte->prioridad]}}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Documentos</h6>
                                        <div class="d-flex" style="border-radius:15px;border:1px solid gray;padding:5px 20px;max-height:300px;overflow:auto;">
                                            <h4>Sin documentos (Logica no esta lista)</h4>
                                        </div>
                                        <br>
                                        <h6>Respuestas (Solo ultimas 3)</h6>
                                        <div class="" style="border-radius:15px;border:1px solid gray;padding:5px 20px;max-height:300px;overflow:auto;">
                                            @foreach($reporte->ultimasRespuestas as $respuesta)
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="card-text">
                                                            <strong> {!! $respuesta->titulo !!}</strong>
                                                            <span class="ml-4" style="margin-left: 25px;"><strong>{{$respuesta->user->name}}</strong> Edito </span>
                                                            <span class="ml-4" style="margin-left: 25px;">Prioridad a: <span class="badge bg-{{$arr_prioridad[$respuesta->prioridad]}} text-{{$arr_txtprioridad[$respuesta->prioridad]}}">{{$arr_labprioridad[$respuesta->prioridad]}}</span></span>
                                                            <span class="ml-4" style="margin-left: 25px;"> Estado a: <span class="badge bg-{{$arr_estadobg[$respuesta->estado]}} text-{{$arr_estadocol[$respuesta->estado]}}">{{$arr_estadolb[$respuesta->estado]}}</span></span>
                                                            <span class="ml-4" style="margin-left: 25px;"> {{\Carbon\carbon::parse($respuesta->created_at)->format('Y-m-d')}}</span>
                                                        </div>
                                                        <div class="card-text">
                                                            {!! $respuesta->descripcion !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            @endforeach
                                            @if(count($reporte->ultimasRespuestas)<=0)
                                                <h4>
                                                    No hay respuestas
                                                </h4>
                                            @endif
                                        </div>
                                        <div style="text-align: center;" x-data="{response:false}">
                                            @if(count($reporte->respuestas)>3)
                                                <a  href="{{ url('/reporte/'.md5($reporte->id)) }}" class="btn btn-dark mt-2">Ver todas las respuestas</a>
                                            @if($reporte->estado ==0 || $reporte->estado ==1 || $reporte->estado ==2)
                                                <a class="btn btn-light mt-2" x-on:click="response=!response">Responder</a>
                                                <div class="card" x-show="response" style="position: absolute;z-index:5;">
                                                    <div class="card-body">
                                                        <form action="{{url('/reporte/response')}}" method="POST">
                                                            @csrf
                                                            <input type="hidden" value="{{md5($reporte->id)}}" name="reporte">
                                                            <div class="form-row row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="name">Titulo de Reporte</label>
                                                                    <input type="text" class="form-control" id="titulo" placeholder="Titulo" required name="titulo" readonly value="Re: {{$reporte->titulo}}">
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="form-row row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="precio">Descripción detallada</label>
                                                                    <textarea class="form-control" id="descripcion{{$reporte->id}}" placeholder="Descripción" required  name="descripcion" rows="4"></textarea>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="form-row row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="tipoDescuento">Area a donde va redirigido <small>(Si cambias este campo, no volveras a ver este reporte hasta que te lo reasignen.)</small></label>
                                                                    @php
                                                                        $areas = \App\level::get();
                                                                    @endphp
                                                                    <select class="form-control areaSelect areaSelect{{$reporte->id}}" id="area{{$reporte->id}}" data-index="{{$reporte->id}}" name="area[]" multiple="multiple" style="width: 100%;">
                                                                        <option value="all"0 data-lvl="all">Todas las Areas</option>
                                                                        @foreach($areas as $area)
                                                                        <option value="{{ md5($area->id) }}" data-lvl="{{ $area->id }}" id="opt{{ $area->id }}">{{ $area->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                        
                                                            <div class="form-row row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="tipoDescuento">Usuario en Especifico <small>(Si cambias este campo, no volveras a ver este reporte hasta que te lo reasignen.)</small></label>
                                                                    <div class="col-12">
                                                                        <select class="form-control areaSelectUsers areaSelectUsers{{$reporte->id}}" id="users{{$reporte->id}}" data-index="{{$reporte->id}}" name="users[]" multiple="multiple" disabled style="width: 100%;"></select>
                                                                    </div>
                                                                </div>
                                                            </div>
                        
                                                            <div class="form-row row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="tipoDescuento">Cambiar Prioridad</label>
                                                                    <select class="form-control " id="tipoDescuento" required  name="prioridad">
                                                                        <option value="2">Alta</option>
                                                                        <option value="1">Media</option>
                                                                        <option value="0">Baja</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                        
                                                            <div class="form-row row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="estado{{$reporte->id}}">Cambiar Estado</label>
                                                                    <select class="form-control estado" id="estado{{$reporte->id}}" required  name="estado" data-index="{{$reporte->id}}">
                                                                        <option value="1">Visto</option>
                                                                        <option value="2">En Revisión</option>
                                                                        <option value="3">Finalizado con Exitó</option>
                                                                        <option value="4">Finalizado sin Exitó</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                        
                                                            <div class="form-row row d-none" id="descFail{{$reporte->id}}">
                                                                <div class="form-group col-md-12">
                                                                    <label for="precio">¿Porqué?</label>
                                                                    <textarea class="form-control" id="fallo{{$reporte->id}}" placeholder="Descripción de porque no fue exitoso el reporte"  name="fallo" rows="4"></textarea>
                                                                </div>
                                                            </div>
                                                            
                                                            <button type="submit" class="btn btn-primary">Guardar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                            <a href="" class="btn btn-secondary mt-2 ">Imprimir</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            {{-- <hr> --}}
            {{-- <script>
                $(document).ready(function() {
                    $('.areaSelect{{$reporte->id}}').select2();
                    $('.areaSelectUsers{{$reporte->id}}').select2();
                    CKEDITOR.replace( 'descripcion{{$reporte->id}}' );
                });
                $(".estado").on( "change", async function(e) {
                    if($(this).val()!=4){
                        if(!$('#descFail'+$(this).attr('data-index')).hasClass('d-none')){
                            $('#descFail'+$(this).attr('data-index')).addClass('d-none');
                            
                            $('#fallo'+$(this).attr('data-index')).removeAttr('required');
                        }
                    }
                    if($(this).val()==4){
                        $('#descFail'+$(this).attr('data-index')).removeClass('d-none');
                        $('#fallo'+$(this).attr('data-index')).attr('required','required');
                    }
                });
            </script>   --}}

    </div>
</div>
<script>

    $(".areaSelect").on( "change", async function(e) {
        
        var id = $(this).val();
        var index = $(this).attr('data-index');
        
        var flagAll = 0,flagNone = 0;
        id.some(async function(value){
            if(value=="all"){
                flagAll = 1;
                flagNone = 0;
                $('.areaSelect'+index).val("all");
            }
        });
        
        var users = await serverResponse({_token:'{{csrf_token()}}', id:id},'/reporte/getUserPerArea');

        var usersSlct='';
        usersSlct='<option value="all">Todos los usarios</option>';
        users.forEach(async function(element){
            usersSlct += `<option value="${element.id}">${element.name}</option>`;
        });
        console.log(usersSlct)
        $('#users'+index).removeAttr('disabled');
        $('#users'+index).html(usersSlct);
    });

    $(".areaSelectUsers").on( "change", async function(e) {
        var usr = $(this).val();
        var index = $(this).attr('data-index');
        
        var flagAll = 0,flagNone = 0;
        usr.some(async function(value){
            if(value=="all"){
                flagAll = 1;
                flagNone = 0;
                $('.areaSelect'+index).val("all");
            }
        });
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
