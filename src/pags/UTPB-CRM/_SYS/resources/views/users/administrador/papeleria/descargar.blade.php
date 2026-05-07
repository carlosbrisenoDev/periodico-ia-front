@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">

                    <h3>Descargar formatos</h3>
                    <hr>
                      {{ csrf_field() }}
                      @if (count($documentos) > 0)
                        <table class="table table-responsive table-striped">
                          <tr>
                            <th>Nombre</th>
                            <th>Formato</th>
                          </tr>
                          @foreach ($documentos as $id => $documento)
                            <tr>
                              <td><a href="/papeleria/nuevo/{{md5($id)}}">{{$documento["nombre"]}}</a></td>
                              <td>{{strtoupper($documento["formato"])}}</td>
                            </tr>
                          @endforeach
                        </table>
                        @else
                          <h4>No hay resultados</h4>
                      @endif
                </div>
            </div>
        </div>
@endsection
