@extends('users.' . Auth::user()->level->alias . '.home')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .app-file-list {
    border: 1px solid #ebebeb;
    }

    .app-file-list .app-file-icon {
    background-color: #F5F5F5;
    padding: 2rem;
    text-align: center;
    font-size: 2rem;
    border-bottom: 1px solid #ebebeb;
    border-top-right-radius: 8px;
    border-top-left-radius: 8px;
    }

    .app-file-list:hover {
    border-color: #d7d7d7;
    }

    .description {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    }

    .fa-download {
    color: #333333;
    cursor: pointer;
    transition: 0.3s
    }

    .fa-download:hover {
    color: #2ec1ac;
    }

    .fa-download:active {
    color: #28df99;
    }

    .credits {
    position: absolute;
    right: 20px;
    bottom: 20px;
    }

    .credits a {
    color: #222222;
    text-decoration: none;
    font-weight: 800;
    }
</style>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>
@endsection
@section('content')
@php
    $reporte = \App\reportes_tickets::where(DB::raw('md5(id)'),$id)->first();
    $exts = ['pdf' => 'fa-file-pdf','png'=>'fa-image','jpg'=>'fa-image','jpeg'=>'fa-image','jfif'=>'fa-image','JPG'=>'fa-image','doc'=>'fa-file-word','docx'=>'fa-file-word'];
    $arr_prioridad = array(0 => 'info',1 => 'warning',2 => 'danger');
    $arr_labprioridad = array(0 => 'Baja',1 => 'Media',2 => 'Alta');
    $arr_txtprioridad = array(0 => 'white',1 => 'black',2 => 'white');

    $arr_estadobg = array(0 => 'light',1 => 'info',2 => 'warning',3 => 'success',4 => 'danger');
    $arr_estadocol = array(0 => 'black',1 => 'black',2 => 'black',3 => 'white',4 => 'white');
    $arr_estadolb = array(0 => 'Pendiente',1 => 'Visto',2 => 'En Revisión',3 => 'Finalizado Con Exitó',4 => 'Finalizado Sin Exitó');
    // $ciudadano = \App\ciudadano::where('id',1)->first();
    // $reporte = \App\reporte::where('ciudadano_id',1)->first();
    $ciudadano = $reporte->ciudadano;
    // ->toSql();
    // dd($reportes);
@endphp
<div class="col-md-12">
    <div class="card card-default large">
        <div class="card-body">
            <div class="clearfix">
            <div class="pull-left">
                <h4>{{$reporte->titulo}}</h4>
                {{-- @if($ciudadano)
                    <h3><a href="/ciudadano/modifyr/{{md5($ciudadano->id)}}">{{$ciudadano->full_name()}}</a></h3>
                @else
                    <h3>Reporte General</h3>
                @endif --}}
                <h4>Información</h4>
            </div>
            <div class="pull-right">
            <br>
            </div>
            </div>
            <hr>
            {{-- <form class="form-horizontal" method="POST" action="/reportes/refresh"> --}}
                <input type="hidden" name="id" value="{{$reporte->id}}">
            <div class="col-md-12">
                <div class="row">
                <div class="col-md-3">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="control-label">Folio</label>
                                <input placeholder="Nombre" disabled id="nombre" type="text" class="form-control large"  value="{{date('Y')}}/00{{$reporte->id}}"  autofocus>
                        </div>
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="control-label">Nombre</label>

                                <input placeholder="Nombre" disabled id="nombre" type="text" class="form-control large" name="nombre" value="{{$reporte->user->name}}"  autofocus>

                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="control-label">Código</label>

                                <input placeholder="Nombre" disabled id="codigo" type="text" class="form-control large" name="nombre" value="{{\Carbon\carbon::parse($reporte->created_at)->format('Y')}}{{$reporte->user->id}}{{$reporte->id}}"  autofocus>

                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">Ciudadano</label>
                                @if($ciudadano)
                                    <input type="email" disabled class="form-control large" value="{{$reporte->ciudadano->full_name()}}">
                                    <input type="hidden" name="ciudadano_id" value="{{$ciudadano->id}}">
                                @else
                                    <input type="email" disabled class="form-control large" value="Reporte General">
                                @endif
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title" class="control-label">Titulo</label>
                                <input type="text" class="form-control large" value="{{$reporte->titulo}}" name="titulo" disabled >
                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                        </div>


                    <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                        <label for="name" class="control-label">Dirigido a</label>
                            <input type="text" id="name" placeholder="Nombre" disabled id="codigo" type="text" class="form-control large" name="usuarios"
                            value="@foreach($reporte->getUsuariosName() as $usernam) {{$usernam->name}}, @endforeach"
                            >
                            {{-- <select requried class="form-control large" name="level_id">
                            @foreach (App\level::where('nivel','>',1)->get() as $level)
                                <option @if($level->id == $reporte->level_id) selected @endif value="{{$level->id}}">{{$level->name}}</option>
                            @endforeach
                            </select> --}}

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                    </div>
                    <div class="form-group{{ $errors->has('prioridad_id') ? ' has-error' : '' }}">
                        <label for="name" class="control-label">Prioridad</label>

                            <select requried class="form-control large" name="prioridad_id" disabled>
                                <option class="badge bg-{{$arr_prioridad[$reporte->prioridad]}} text-{{$arr_txtprioridad[$reporte->prioridad]}}" value="{{$reporte->prioridad}}">{{$arr_labprioridad[$reporte->prioridad]}}</option>
                            </select>

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                    </div>
                    <div class="col-md-12" x-data="{response:false}">
                        {{-- <a href="/reportes/uploadr/{{md5($reporte->id)}}" type="submit" class="btn btn-success large" x-on:click="response=!response"> --}}
                        <a class="btn btn-success large" x-on:click="response=!response">
                        <i class="fa fa-upload"></i>  Responder
                        </a>
                        <div class="card" x-show="response" style="position: absolute;z-index:5;margin-top: -25%;margin-left: 25%;">
                            <div class="card-body">
                                <form action="{{url('/reporte/response')}}" method="POST">
                                    @csrf
                                    <input type="hidden" value="{{md5($reporte->id)}}" name="reporte">
                                    <div class="form-row row">
                                        <div class="form-group col-md-12">
                                            <label for="name">Titulo de Respuesta a Reporte</label>
                                            <input type="text" class="form-control" id="titulo" placeholder="Titulo" required name="titulo" readonly value="Re: {{$reporte->titulo}}">
                                        </div>
                                    </div>
    
                                    <div class="form-row row">
                                        <div class="form-group col-md-12">
                                            <label for="precio">Descripción de Respuesta detallada</label>
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
                                    <button type="button" class="btn btn-danger" x-on:click="response=false">Cancelar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="control-label">Descripción</label>

                            <textarea class="form-control large" placeholder="Descripción" name="descripcion" id="descripcion" required autofocus>{!! $reporte->descripcion !!}</textarea>

                            @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                            @endif
                    </div>
                    <div class="col-md-12"  id="drop">
                        <div class="card cursor-pointer">
                            <div class="card-body">
                              <form action="/reporte/fileupload?rep_id={{md5($reporte->id)}}" class="dropzone" id="dropzone">
                                <div class="fallback">
                                    {{-- <input type="hidden" name="rep_id" value="{{md5($reporte->id)}}"> --}}
                                    <input name="file" type="file" multiple />
                                </div>
                              </form>
                            </div>
                          </div>
                        <hr>
                    </div>
                    <div class="col-md-12 leftLine">
                    <div class="col-md-12 row" id="files-uploads">
                        @if (count($reporte->documentos)>0)
                            @foreach($reporte->documentos as $file)
                                    {{-- <div class="col-md-2">
                                        <div class="thumbnail topp">
                                            <div class="img img-responsive text-center">
                                            <i class="fa fa-file"></i>
                                            </div>
                                            <span class="texto">{{$file->titulo.'.'.$file->ext ?? 'Documento Subido'}}</span>
                                            <div class="clearfix">
                                            <div class="col-md-4 nopadding">
                                                <a href="{{$file->url}}" class="btn btn-default"><i class="fa fa-download"></i></a>
                                            </div> --}}
                                            {{-- <div class="col-md-4 nopadding">
                                                <a target="_blank" href="{{$reporte->file_upload}}" class="btn btn-default"><i class="fa fa-eye"></i></a>
                                            </div> --}}
                                            {{-- <div class="col-md-4 nopadding">
                                                <a href="/documentos/trash/{{md5($documento->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                            </div> --}}
                                            {{-- </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-2">
                                        <div class="card app-file-list">
                                            <div class="app-file-icon">
                                                <i class="fas {{$exts[$file->ext]}}" style="color: red; font-size: 60px"></i>
                                            </div>
                                            <div class="description">
                                                <div>
                                                    <div>{{$file->titulo.'.'.$file->ext ?? 'Documento Subido'}}</div>
                                                    <div style="color: #AFAFAF">{{$file->size}}b</div>
                                                </div>
                                                <a href="{{$file->url}}">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach 
                            @else
                                <div class="col-md-12">
                                    <h4>No hay documentos</h4>
                                </div>
                            @endif
                            
                            
                    </div>
                    </div>
                    <div class="col-md-12">
                    <hr>
                    Últimas 3 notas agregadas
                        @foreach($reporte->ultimasRespuestas as $respuesta)
                        <div class="leftLine">
                            <b>{{$respuesta->user->name}}</b>
                            <p align)="justify">
                                {!! $respuesta->descripcion !!}
                            </p>
                            <p align="right">{{$respuesta->full_fecha()}}</p>
                        </div>
                        <hr>
                        @endforeach
                    </div>
                </div>
                </div>
            </div>
            <div class="col-md-12 nopadding">
                <hr>
                <div class="row">
                <div class="col-md-3">
                    {{-- <button type="submit" class="btn btn-primary large">
                    <i class="fa fa-refresh"></i>    Actualizar
                    </button> --}}
                {{-- </form> --}}
                </div>
                

                {{-- <div class="col-md-3">
                    <a href="/reportes/notasr/{{md5($reporte->id)}}" class="btn btn-success large">
                    <i class="fa fa-pencil"></i>    Notas
                </a>
                </div>
                <div class="col-md-3">
                    <a target="_blank" href="/reportes/imprimirr/{{md5($reporte->id)}}" class="btn btn-warning large">
                    <i class="fa fa-print"></i>    Imprimir
                </a>
                </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <div class="content mt-5">
    <h4>Reporte #{{$reporte->id}} - Repuestas</h4>
    <div class="container">
            <div class="card">
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
                    @foreach($reporte->respuestas as $respuesta)
                        <div class="card">
                            <div class="card-header">
                                <strong> {!! $respuesta->titulo !!}</strong>
                                <span class="ml-4" style="margin-rigth: 25px;"> De: {{$respuesta->user->name}}</span>

                                <span class="ml-4" style="margin-left: 25px;">Cambio Prioridad a: <span class="badge bg-{{$arr_prioridad[$respuesta->prioridad]}} text-{{$arr_txtprioridad[$respuesta->prioridad]}}">{{$arr_labprioridad[$respuesta->prioridad]}}</span></span>
                                <span class="ml-4" style="margin-left: 25px;">Cambio Estado a: <span class="badge bg-{{$arr_estadobg[$respuesta->estado]}} text-{{$arr_estadocol[$respuesta->estado]}}">{{$arr_estadolb[$respuesta->estado]}}</span></span>
                            </div>
                            <div class="card-body">
                                {!! $respuesta->descripcion !!}
                            </div>
                        </div>
                        <hr>
                    @endforeach

                </div>
            </div>
            <hr>

    </div>
</div> --}}
{{-- <div class="content">
    <div class="card" x-data="{rep:true}">
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
                            <div class="mb-4" style="border-radius:15px;border:1px solid gray;padding:5px 20px;max-height:300px;overflow:auto;">
                                {!! $reporte->descripcion !!}
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
                            <h6>Respuestas </h6>
                            <div class="" style="border-radius:15px;border:1px solid gray;padding:5px 20px;max-height:700px;overflow:auto;">
                                @foreach($reporte->respuestas as $respuesta)
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-text">
                                                <strong> {!! $respuesta->titulo !!}</strong>
                                                <span class="ml-4" style="margin-left: 25px;">{{$respuesta->user->name}} Cambio Prioridad a: <span class="badge bg-{{$arr_prioridad[$respuesta->prioridad]}} text-{{$arr_txtprioridad[$respuesta->prioridad]}}">{{$arr_labprioridad[$respuesta->prioridad]}}</span></span>
                                                <span class="ml-4" style="margin-left: 25px;"> Cambio Estado a: <span class="badge bg-{{$arr_estadobg[$respuesta->estado]}} text-{{$arr_estadocol[$respuesta->estado]}}">{{$arr_estadolb[$respuesta->estado]}}</span></span>
                                                <span class="ml-4" style="margin-left: 25px;">{{\Carbon\carbon::parse($respuesta->created_at)->format('Y-m-d')}}</span>
                                            </div>
                                            <div class="card-text">
                                                {!! $respuesta->descripcion !!}
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                @endforeach
                            </div>
                            <div style="text-align: center;" x-data="{response:false}">
                                <a href="" class="btn btn-dark mt-2">Ver todas las respuestas</a>
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
<script src="{{ asset('js/dropzone.js') }}"></script>
<script>
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
</script>
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script>
    $(document).ready(function() {
      var myDropzone = new Dropzone("#dropzone");
      $(".dz-message").html(`<div class="dz-default dz-message" style="
            text-align: center;
        "><i class="fas fa-file-upload fa-2x mb-3" aria-hidden="true"></i><div class="h5" style="margin-left:20px;">Arrastra y suelta aquí archivos para adjuntarlos</div></div>
        `);
      myDropzone.on("addedfile", function(file) {
        $(".enviar").addClass("disabled");
      });
      myDropzone.on("complete", function(file) {
        $(".enviar").removeClass("disabled");
      });
      myDropzone.on("success", function(file,data) {
        if($('#files-uploads').find('div.col-md-12').length !== 0){
            $('#files-uploads').html('');
        }
        Dropzone.forElement('#dropzone').removeAllFiles(true)
        $('#files-uploads').append(`<div class="col-md-2">
            <div class="thumbnail topp">
                <div class="img img-responsive text-center">
                <i class="fa fa-file"></i>
                </div>
                <span class="texto">${file.name}</span>
                <div class="clearfix">
                <div class="col-md-4 nopadding">
                    <a href="${data}" class="btn btn-default"><i class="fa fa-download"></i></a>
                </div>
                
                </div>
            </div>
        </div>`)
        
      });
    });

    CKEDITOR.replace( 'descripcion' , {
        readOnly:true,
        toolbar: [
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
        ]
        });
    CKEDITOR.config.removePlugins = 'Save,Print,Preview,Find,About,Maximize,ShowBlocks';
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
