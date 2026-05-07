<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ayuntamiento de Fortin - Sistema integral @yield('title')</title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
</head>
<body>
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body" id="card-body">
                  <div class="clearfix">
                    <div class="pull-left">
                      @php
                        $ciudadano = $reporte->ciudadano;
                      @endphp
                      <h3>{{$ciudadano->full_name()}}</h3>
                      <h4>Información de reporte</h4>
                    </div>
                    <div class="pull-right">
                    </br>
                    </div>
                  </div>
                  <hr>
                    <div class="col-md-12">
                        <table class="table table-striped">
                          <tr>
                            <td>Folio</td>
                            <td>{{$reporte->id}}</td>
                          </tr>
                          <tr>
                            <td>Nombre del reporte</td>
                            <td>{{$reporte->nombre}}</td>
                          </tr>
                          <tr>
                            <td>Fecha de creación</td>
                            <td>{{$reporte->created_at}}</td>
                          </tr>
                          <tr>
                            <td>Fecha de actualización</td>
                            <td>{{$reporte->updated_at}}</td>
                          </tr>
                          <tr>
                            <td>Nombre del ciudadano</td>
                            <td>{{$reporte->ciudadano->full_name()}}</td>
                          </tr>
                          <tr>
                            <td>Código de ciudadano</td>
                            <td>{{$reporte->ciudadano->codigo}}</td>
                          </tr>
                          <tr>
                            <td>Teléfono</td>
                            <td>{{$reporte->ciudadano->telefono}}</td>
                          </tr>
                          <tr>
                            <td>Localidad</td>
                            <td>{{$reporte->ciudadano->localidad}}</td>
                          </tr>
                          <tr>
                            <td>Colonia</td>
                            <td>{{$reporte->ciudadano->colonia}}</td>
                          </tr>
                          <tr>
                            <td>Dirección</td>
                            <td>{{$reporte->ciudadano->direccion}}</td>
                          </tr>
                          <tr>
                            <td>Área a quien va dirigido</td>
                            <td>{{$reporte->level->name}}</td>
                          </tr>
                          <tr>
                            <td>Descripción</td>
                            <td>{{$reporte->descripcion}}</td>
                          </tr>
                          <tr>
                            <td>Prioridad</td>
                            <td>{{$reporte->prioridad->nombre}}</td>
                          </tr>
                          <tr>
                            <td>Estado del reporte</td>
                            <td>{{$reporte->estado->nombre}}</td>
                          </tr>
                          <tr>
                            <td>Cantidad de documentos anexos</td>
                            <td>{{count($reporte->documentos)}}</td>
                          </tr>
                          <tr>
                            <td>Título de los documentos anexos</td>
                            <td>
                              @if (count($reporte->documentos) > 0)
                                @foreach ($reporte->documentos as $d)
                                  {{$d->titulo}},
                                @endforeach
                                @else
                                  No aplica
                              @endif
                            </td>
                          </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
          <a href="#" class="printo">Imprimir</a>
        </div>
        <script src="{{ asset('js/jquery-2.2.2.min.js') }}"></script>
        <script type="text/javascript">
          $(".printo").bind('click',function(){
            window.print();
          });
        </script>
</body>
</html>
