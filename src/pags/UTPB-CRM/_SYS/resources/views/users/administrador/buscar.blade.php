@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
{{-- <div class="card">
  <form class="form-horizontal" method="POST" action="/user/search">
    @csrf
    <div class="card-header">
      <h4>Buscar</h4>
    </div>
    <div class="card-body">
      <div class="form-group">
        <label for="email"><h5>Correo</h5></label>
        <input type="text" placeholder="Correo electrónico" value="{{((Request::has('buscar')) ? urldecode(Request::get('buscar')) :"")}}" required id="email" type="text" class="form-control" name="search" required autofocus>
        @if ($errors->has('email'))
          <span class="help-block">
              <strong>{{ $errors->first('email') }}</strong>
          </span>
        @endif
      </div>
      <button type="submit" class="btn btn-primary">
        <i class="fa fa-search"></i>    Buscar
      </button>
    </div>
  </form>
</div> --}}
        {{-- <div class="col-12">
            <div class="card card-default">
                <div class="card-body">
                    <div class="clearfix">
                      <div class="float-start">
                      </div>
                      <div class="float-end">
                        <form class="form-horizontal" method="POST" action="/user/search">
                          {{ csrf_field() }}
                          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                              <div class="col">
                                <div class="input-group">
                                    <input autofocus placeholder="Correo electrónico" value="{{((Request::has('buscar')) ? urldecode(Request::get('buscar')) :"")}}" required id="name" type="text" class="form-control" name="search" required autofocus>
                                    <div class="input-group-btn">
                                      <button type="submit" class="btn btn-primary">
                                      <i class="fa fa-search"></i>    Buscar
                                      </button>
                                    </div>
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
                      <hr>
                    </div>
                </div>
            </div>
        </div> --}}
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table" id="usertable">
              <thead>
                  <tr>
                      <th class="text-center text-black">#</th>
                      <th class=" text-black">Nombre</th>
                      <th class="text-black">Correo</th>
                      <th class="text-black">Telefono</th>
                      {{-- <th class="text-right">Costo Total</th> --}}
                      <th class="text-black">Rol</th>
                      <th class="text-black">Cargo</th>
                      <th class="text-black">Sucursal</th>
                      <th></th>
                  </tr>
              </thead>
              <tbody>
                
                  @foreach(\App\user::get() as $user)
                      <tr>
                          <td class="text-center">{{$user->id}}</td>
                          <td>{{$user->name}}</td>
                          <td>{{$user->email  ?? 'Sin Datos'}}</td>
                          <td>{{$user->telefono ?? 'Sin Datos'}}</td>
                          <td>{{$user->levels->name ?? 'Sin Datos'}}</td>
                          <td>{{$user->cargo ?? 'Sin Datos'}}</td>
                          <td>{{$user->sucursal ?? 'Sin Datos' }}</td>
                          
                          {{-- <td class="text-right">&euro; 99,225</td> --}}
                          <td class="td-actions text-right">
                              {{-- <a type="button" rel="tooltip" class="btn btn-info" href="#">
                                  <i class="material-icons">Ver</i>
                              </a> --}}
                              <a type="button" rel="tooltip" class="btn btn-success" href="{{url('/user/modify/'.md5($user->id))}}">
                                  <i class="material-icons">edit</i>
                              </a>
                              {{-- <form action="{{url('/productos/destroy')}}" method="POST">
                                  @csrf
                                  <input type="hidden" name="prod_id" value="{{$producto->id}}">
                                  <button type="submit" rel="tooltip" class="btn btn-danger">
                                      <i class="material-icons">Borrar</i>
                                  </button>
                              </form> --}}
                          </td>
                      </tr>
                  @endforeach
              </tbody>
          </table>
        </div>
      </div>
    </div>
@endsection
@section('scripts')
<script>
  $('#usertable').DataTable();
</script>
@endsection