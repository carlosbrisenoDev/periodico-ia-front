@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="card">
    <div class="card-body">
      <div class="clearfix">
        <div class="float-start">
          <h6>Sedes</h6>
        </div>
        <div class="float-end">
          <a class="btn btn-primary" href="/administrador/crearsede">
            Nuevo
          </a>
        </div>
      </div>
      <hr>
      <table class="table table-stripped">
        <thead>
          <tr class="bg-dark">
            <th>Sede</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach (\App\sedes::all() as $sede)
            <td>{{$sede->sede}}</td>
            <td class="text-right">
              <a href="/sedes/eliminar/do?cid={{md5($sede->id)}}">
                <i class="fa fa-trash"></i>
              </a>
            </td>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
