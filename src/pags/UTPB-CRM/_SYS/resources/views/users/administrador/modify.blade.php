@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <a href="{{url('/administrador/buscar')}}" class="btn btn-info">Regresar</a>
        <div class="row">
          <div class="col-12 col-md-6 col-lg-4 col-xl-4">
              <div class="card card-default">
                  <div class="card-body">
                      <h3>Modificar usuario</h3>
                      <hr>
                      <form class="form-horizontal" method="POST" action="/user/refresh">
                        <input type="hidden" name="id" value="{{$user->id}}">
                      <div class="col">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Encargado</label>

                                <div class="col">
                                    <input placeholder="Nombre del encargado" value="{{$user->name}}" required id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">Correo electrónico</label>

                                <div class="col">
                                    <input placeholder="Correo electrónico" disabled value="{{$user->email}}" id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Clave</label>

                                <div class="col">
                                    <div class="input-group">
                                      <input placeholder="Clave" id="password" value="{{$user->codigo2}}" required type="text" class="form-control" name="password">
                                      <div class="input-group-append">
                                        <button class="btn btn-outline-secondary generar" type="button" id="button-addon2">Generar</button>
                                      </div>
                                    </div>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Área de desarrollo</label>

                            <div class="col">
                                <select requried class="form-control" name="level_id">
                                  @php
                                    $levelid = (isset($user->level->id)) ? $user->level->id : 0;
                                  @endphp
                                  @foreach (App\level::whereNotIn('name',["Empleado"])->get() as $level)
                                    <option @if($levelid == $level->id) selected @endif value="{{$level->id}}">{{$level->name}}</option>
                                  @endforeach
                                </select>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('ccuser') ? ' has-error' : '' }}">
                          <label for="name" class="col-md-4 control-label">Usuario de Call Center</label>

                          <div class="col">
                              <input placeholder="Cuenta de Call Center del encargado" value="{{$user->ccuser}}" id="ccuser" type="text" class="form-control" name="ccuser" value="{{ old('ccuser') }}" autofocus>

                              @if ($errors->has('ccuser'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('ccuser') }}</strong>
                                  </span>
                              @endif
                          </div>
                        </div>

                        <div class="form-group{{ $errors->has('ccpassword') ? ' has-error' : '' }}">
                          <label for="name" class="col-md-4 control-label">Password de User de Call Center</label>

                          <div class="col">
                              <input placeholder="Cuenta de Call Center del encargado" value="{{$user->ccpassword}}" id="ccpassword" type="text" class="form-control" name="ccpassword" value="{{ old('ccpassword') }}" autofocus>

                              @if ($errors->has('ccpassword'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('ccpassword') }}</strong>
                                  </span>
                              @endif
                          </div>
                        </div>

                      </div>
                      <div class="col-md-6">

                      </div>
                      <div class="col-md-12">
                              <br>
                              <br>
                              <div class="row">
                                <div class="col-12 col-md-12 col-lg-6">
                                    <button type="submit" class="btn btn-primary large">
                                    <i class="fa fa-refresh"></i>    Actualizar
                                    </button>
                                </div>                  </form>

                                <div class="col-12 col-md-12 col-lg-6">
                                  <form class="form-horizontal" method="POST" action="/user/trash">
                                    <input type="hidden" name="id" value="{{$user->id}}">
                                        {{ csrf_field() }}
                                    <button type="submit" class="btn btn-danger large">
                                    <i class="fa fa-trash"></i>    Borrar
                                    </button>
                                  </form>
                                </div>
                              </div>
                        </div>
                      </div>
                  </div>
                </div>
          <div class="col-8">
            <div class="card card-default large">
                <div class="card-body">
                    <h6>Roles</h6>
                    <hr>
                    <form action="/user/roles" method="post">
                      <input type="hidden" name="cid" value="{{md5($user->id)}}">
                      @php
                      $roles = [];
                      foreach ($user->role as $value) {
                        array_push($roles,$value->role_id);
                      }
                      @endphp
                      <select class="form-control" name="roles[]" name="roles" multiple>
                        @foreach (\App\role::all() as $role)
                          <option {{in_array($role->id,$roles) ? "selected" : ""}}  value="{{$role->id}}">{{$role->role}}</option>
                        @endforeach
                      </select>
                      <br>
                      <div class="row">
                        <div class="col">
                          <div class="clearfix">
                            <div class="float-end">
                              <input type="submit" name="" value="Guardar" class="btn btn-primary">
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                </div>
            </div>
            <br>
            <div class="card">
              <div class="card-body">
                <h6>Asginar sede</h6>
                <hr>
                <form action="/sedes/set" method="post">
                  <input type="hidden" name="cid" value="{{md5($user->id)}}">
                  <div class="input-group mb-3">
                    <div class="input-gropup-prepend">
                       <span class="input-group-text">Sede: </span>
                    </div>
                    <select class="form-control" name="sede_id">
                      <option value="">Selecciona una sede</option>
                      @foreach (\App\sedes::all() as $sede)
                        <option {{$user->sede_id == $sede->id ? "selected" : ""}} value="{{$sede->id}}">{{$sede->sede}}</option>
                      @endforeach
                    </select>
                    <div class="input-group-append">
                      <input type="submit" class="btn btn-primary" value="Guardar">
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <br>
            <div class="card">
              <div class="card-body">
                <h6>Correo electrónico:</h6>
                <hr>
                {{-- @php
                  use newXMLAPI\newXMLAPI as api;
                  $api = new api();
                  $username = explode("@",$user->email);
                  $r = json_decode($api->checkExists("unisantorizaba.com",$username[0],$user->codigo2));
                @endphp
                @if(isset($r))
                  @if (count($r->cpanelresult->data) > 0)
                    <div class="alert alert-success">
                      <i class="fa fa-envelope"></i>
                      Cuenta de correo electrónico en linea
                    </div>
                    @else
                      <div class="alert alert-warning">
                        <div class="clearfix">
                          <div class="float-start">
                            <i class="fa fa-alert"></i>
                            No se ha podido crear la cuenta de correo electrónico
                          </div>
                          <div class="float-end">
                            <a href="#" class="btn btn-default">Reintentar</a>
                          </div>
                        </div>
                      </div>
                  @endif
                @endif --}}
              </div>
            </div>
          </div>
        </div>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(".generar").bind("click",function(){
      var letters = "asdfghjklñqwertyuiopzxcvbnmQWERTYUIOPASDFGHJKLÑZXCVBNM7894561230.-_";
      var length = 8;
      var password = "";
      for (var i = 0; i < length; i++) {
        var rnd = Math.round(Math.random() * letters.length);
        password += letters.substring(rnd-1,rnd);
      }
      $("#password").val(password);
    });
  </script>
@endsection
