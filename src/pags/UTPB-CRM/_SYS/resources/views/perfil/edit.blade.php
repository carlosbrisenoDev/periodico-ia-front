@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="row">
          <div class="col-12 col-md-12 col-lg-12 col-xl-12">
              <div class="card card-default">
                  <div class="card-body">
                      <h3>Modificar usuario</h3>
                      <hr>
                      <form class="form-horizontal" method="POST" action="/home/updateinfo">
                        <input type="hidden" name="id" value="{{$user->id}}">
                        <div class="col">
                            {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nombre</label>

                            <div class="col">
                                <input placeholder="Nombre Completo" value="{{$user->name}}" required id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

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

                        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label for="phone" class="col-md-4 control-label">Telefono</label>

                            <div class="col">
                                <input placeholder="Telefono del encargado" value="{{$user->telefono}}" required id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" required autofocus>

                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('cargo') ? ' has-error' : '' }}">
                            <label for="cargo" class="col-md-4 control-label">Cargo</label>

                            <div class="col">
                                <input placeholder="Cargo" value="{{$user->cargo}}" required id="cargo" type="text" class="form-control" name="cargo" value="{{ old('cargo') }}" required autofocus>

                                @if ($errors->has('cargo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cargo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Área de desarrollo</label>

                            <div class="col">
                                <select requried class="form-control" name="level_id" disabled>
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
                                    </div>                  
                                </form>
                                </div>
                            </div>
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
