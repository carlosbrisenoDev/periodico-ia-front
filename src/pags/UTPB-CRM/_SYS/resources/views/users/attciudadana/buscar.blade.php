@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">

                    <h3>Buscar</h3>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/ciudadano/search">
                      {{ csrf_field() }}
                      <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                          <div class="col-md-3">
                            <div class="input-group">
                                <input placeholder="Nombre ó Curp ó Correo electrónico" required id="name" type="text" class="form-control large" name="search" required autofocus>
                                <div class="input-group-btn">
                                  <button type="submit" class="btn btn-primary large">
                                  <i class="fa fa-search"></i>    Buscar
                                  </button>
                              </div>
                            </div>
                              @if ($errors->has('email'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('email') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>
                    </form>
                </div>
            </div>
        </div>
@endsection
