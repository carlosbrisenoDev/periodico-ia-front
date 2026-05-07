@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                      <h3>Permisos emitidos</h3>
                      <p>Asigna permiso a otras áreas para que puedan ver tu agenda</p>
                      <hr>
                        <form class="" action="/agenda/asignar" method="post">
                          <div class="col-12 nopadding">
                            <div class="col-md-1">
                                <label for="">Área</label>
                            </div>
                            <div class="input-group">
                              <select class="form-control" name="level_id">
                                @foreach (\App\level::where('id','>','1')->get() as $level)
                                  <option value="{{$level->id}}">{{$level->name}}</option>
                                @endforeach
                              </select>
                              <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-plus"></i> Asignar permisos</button>
                              </div>
                            </div>
                          </div>
                        </br>
                        </br>
                        </br>
                        </form>
                        {{ csrf_field() }}
                        @php
                          $permisos = \App\permisa::where('level_id',Auth::user()->level->id)->get();
                        @endphp
                        @if (count($permisos) > 0)
                          <table class="table table-stripped">
                            <tr class="bg-primary">
                              <th>Área</th>
                              <th></th>
                            </tr>
                            @foreach ($permisos as $permiso)
                              <tr>
                                <td>{{$permiso->usuario->name}}</td>
                                <td style="width:40px;"><a class="btn btn-default" href="/agenda/zap/{{md5($permiso->id)}}"><i class="fa fa-trash"></i></a></td>
                              </tr>
                            @endforeach
                          </table>
                          @else
                            <div class="col-md-12">
                              <hr>
                              <h4>Nadie más puede ver tu agenda</h4>
                            </div>
                        @endif
                        <div class="col-md-12 nopadding">
                          <hr>
                          <h3>Mis permisos</h3>
                          <p>Puedes ver la agenda de las siguientes áreas:</p>
                            {{ csrf_field() }}
                            @php
                              $permisos = (Auth::user()->level->name == "Alcalde") ? \App\level::all() : \App\permisa::where('current_id',Auth::user()->level->id)->get();
                            @endphp
                            @if (count($permisos) > 0)
                              <table class="table table-stripped">
                                <tr>
                                  <th class="bg-primary">Área</th>
                                </tr>
                                @foreach ($permisos as $permiso)
                                  <tr>
                                    <td>{{(Auth::user()->level->name=="Alcalde") ? $permiso->name : $permiso->level->name}}</td>
                                  </tr>
                                @endforeach
                              </table>
                              @else
                                <div class="col-md-12">
                                  <h4>No puedes ver la agenda de nadie más</h4>
                                </div>
                            @endif
                        </div>
                </div>
            </div>
        </div>
@endsection
