@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">

                    <h3>Mis formatos</h3>
                    <hr>
                      {{ csrf_field() }}
                      @if (count($documentos) > 0)
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
                          @foreach ($documentos as $id => $documento)
                            <tr>
                              <td>{{$documento->asunto}}</td>
                              <td>{{$documento->nombre()}}</td>
                              <td>{{Auth::user()->level->name}} ({{\App\User::find($documento->user_id)->name}})</td>
                              <td>{{$documento->destino}}</td>
                              <td>{{Date("Y")."/".str_pad($documento->id, 4, "0", STR_PAD_LEFT)}}</td>
                              <td>{{$documento->full_fecha()}}</td>
                              <td>
                                @if ($documento->archivo == "0")
                                    <a href="/papeleria/subir/{{md5($documento->id)}}" style="color:red;"><i class="fa fa-upload"></i> (Subir documento escaneado)</a>
                                  @else
                                    <a href="/papeleria/subir/{{md5($documento->id)}}"><i class="fa fa-download"></i> Descargar documento escaneado</a>
                                @endif
                              </td>
                              <td>
                                @if ($documento->archivo == "0")
                                    <a href="/papeleria/descargar/{{md5($documento->id)}}" style="color:red;"><i class="fa fa-download"></i> Descargar plantilla</a>
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
