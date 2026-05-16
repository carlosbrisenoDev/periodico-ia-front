@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                    <div class="col-md-12">
                      <div class="form-group">
                          <div class="col-md-9">
                            <h5>¿Desea eliminar al ciudadano <i>{{\App\ciudadano::find($id)->email}}</i>?</h5>
                          </div>
                          <div class="col-md-3">
                            <div class="col-md-6">
                                <a href="/ciudadano/modify/{{md5($id)}}" class="btn btn-danger large">
                                <i class="fa fa-cancel"></i>    No, Regresar
                              </a>
                            </div>
                            <div class="col-md-6">
                              <form class="form-horizontal" method="POST" action="/ciudadano/secure">
                                <input type="hidden" name="id" value="{{$id}}">
                                    {{ csrf_field() }}
                                <button type="submit" class="btn btn-success large">
                                <i class="fa fa-done"></i>    Si, Eliminar
                                </button>
                              </form>
                            </div>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
