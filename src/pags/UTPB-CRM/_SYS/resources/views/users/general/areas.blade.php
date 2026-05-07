@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <h3>Directorio de personal por área</h3>
                    <hr>
                    @php
                      $areas = App\level::all()->whereNotIn("name",["Empleado"])->except([0,1]);
                    @endphp
                    @if (count($areas) > 0)
                      <div class="row">
                        @foreach ($areas as $level)
                            @if(count($level->usuarios) > 0)
                              <div class="col-12">
                                  <h4 class="titulo">{{$level->name}}</h4>
                                    <table class="table table-stripped">
                                    @foreach ($level->usuarios as $usuario)
                                          <tr>
                                            <td style="width:400px;">{{$usuario->name}}</td>
                                            <td>
                                              <a href="/bandeja/nuevo/enviar?a={{$usuario->email}}">{{$usuario->email}}</a>
                                            </td>
                                          </tr>
                                    @endforeach
                                    </table>
                              </div>
                            @endif
                        @endforeach
                      </div>
                      @else
                        <div class="col-md-12">
                          <h4>No hay resultados</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
@endsection
