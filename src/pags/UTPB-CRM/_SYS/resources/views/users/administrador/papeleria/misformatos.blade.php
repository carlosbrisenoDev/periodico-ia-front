@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">

                    <h3><a href="/historial/area/{{md5($area->id)}}">{{$area->name}}</a></h3>
                    <h3>Formatos</h3>
                    <hr>
                      {{ csrf_field() }}
                      @if (count($formatos) > 0)
                        <table class="table table-responsive table-striped">
                          <tr>
                            <th>Asunto</th>
                            <th>Formato</th>
                            <th>Origen</th>
                            <th>Destinatario</th>
                            <th>Referencia</th>
                            <th>Fecha</th>
                            <th></th>
                            <th></th>
                          </tr>
                          @foreach ($formatos as $id => $formato)
                            <tr>
                              <td>{{$formato->asunto}}</td>
                              <td>{{$formato->nombre()}}</td>
                              <td>{{Auth::user()->level->name}} ({{\App\User::find($formato->user_id)->name}})</td>
                              <td>{{$formato->destino}}</td>
                              <td>{{Date("Y")."/".str_pad($formato->id, 4, "0", STR_PAD_LEFT)}}</td>
                              <td>{{$formato->full_fecha()}}</td>
                              <td>
                                @if ($formato->archivo == "0")
                                    <a href="/papeleria/subir/{{md5($formato->id)}}" style="color:red;"><i class="fa fa-upload"></i> (Subir formato escaneado)</a>
                                  @else
                                    <a href="/papeleria/subir/{{md5($formato->id)}}"><i class="fa fa-download"></i> Descargar formato escaneado</a>
                                @endif
                              </td>
                              <td>
                                @if ($formato->archivo == "0")
                                    <a href="/papeleria/descargar/{{md5($formato->id)}}" style="color:red;"><i class="fa fa-download"></i> Descargar plantilla</a>
                                @endif
                              </td>
                            </tr>
                          @endforeach
                        </table>
                        @else
                          <h4>Vácio</h4>
                      @endif
                </div>
            </div>
        </div>
@endsection
