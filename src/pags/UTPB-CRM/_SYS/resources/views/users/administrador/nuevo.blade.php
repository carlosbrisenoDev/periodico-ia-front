@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                    <h3>Nuevo usuario</h3>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/user/create">
                    <div class="col-md-6">
                          {{ csrf_field() }}

                          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                              <label for="name" class="col-md-4 control-label">Encargado</label>

                              <div class="col-md-6">
                                  <input placeholder="Nombre del encargado" required id="name" type="text" class="form-control large" name="name" value="{{ old('name') }}" required autofocus>

                                  @if ($errors->has('name'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('name') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>

                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="email" class="col-md-4 control-label">Correo electrónico</label>

                              <div class="col-md-6">
                                  <input placeholder="Correo electrónico" required id="email" type="email" class="form-control large" name="email" value="{{ old('email') }}" required>

                                  @if ($errors->has('email'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>

                          <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                              <label for="password" class="col-md-4 control-label">Clave</label>

                              <div class="col-md-6">
                                  <div class="input-group">
                                    <input placeholder="Clave" required id="password" type="text" class="form-control" name="password" required>
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
                          <label for="name" class="col-md-4 control-label">Nivel</label>

                          <div class="col-md-6">
                              <select requried class="form-control large lvl" name="level_id">
                                @foreach (App\level::whereNotIn('name',["Empleado"])->get() as $level)
                                  <option value="{{$level->id}}">{{$level->name}}</option>
                                @endforeach
                              </select>

                              @if ($errors->has('name'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('name') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>

                      {{-- <div class="form-group{{ $errors->has('ccuser') ? ' has-error' : '' }}">
                        <label for="ccuser" class="col-md-4 control-label">Usuario de CC</label>

                        <div class="col-md-6">
                            <input placeholder="Usuario de Call Center" required id="ccuser" type="text" class="form-control large" name="ccuser" value="{{ old('ccuser') }}" required autofocus>

                            @if ($errors->has('ccuser'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ccuser') }}</strong>
                                </span>
                            @endif
                        </div>
                      </div>

                      <div class="form-group{{ $errors->has('ccpassword') ? ' has-error' : '' }}">
                        <label for="ccpassword" class="col-md-4 control-label">Contraseña de CC</label>

                        <div class="col-md-6">
                            <input placeholder="Contraseña de Call Center" required id="ccpassword" type="text" class="form-control large" name="ccpassword" value="{{ old('ccpassword') }}" required autofocus>

                            @if ($errors->has('ccpassword'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ccpassword') }}</strong>
                                </span>
                            @endif
                        </div>
                      </div> --}}

                      <div class="link hidden form-group{{ $errors->has('sucursal') ? ' has-error' : '' }}">
                          <label for="sucursal" class="col-md-4 control-label">Sucursal</label>

                          <div class="col-md-6">
                            <select requried class="form-control large sucursal" name="sucursal">
                              <option value="0">Seleccione</option>
                              @foreach (App\sucursal::all() as $suc)
                                <option value="{{$suc->id}}">{{$suc->nombre}}</option>
                              @endforeach
                            </select>
                          </div>
                      </div>

                      <div class="form-group">
                          <div class="col-md-6 col-md-offset-4">
                              <button type="submit" class="btn btn-primary large">
                              <i class="fa fa-save"></i>    Guardar
                              </button>
                          </div>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(".lvl").bind("change",function(){
      if($(this).find(":selected").text() == "Sucursal"){
        $(".link").removeClass("hidden");
      } else {
        $(".link").addClass("hidden");
      }
    });
    $(".generar").bind("click",function(){
      var letters = "asdfghjklñqwertyuiopzxcvbnmQWERTYUIOPASDFGHJKLÑZXCVBNM7894561230.-_";
      var length = 15;
      var password = "";
      for (var i = 0; i < length; i++) {
        var rnd = Math.round(Math.random() * letters.length);
        password += letters.substring(rnd-1,rnd);
      }
      $("#password").val(password);
    });
  </script>
@endsection
