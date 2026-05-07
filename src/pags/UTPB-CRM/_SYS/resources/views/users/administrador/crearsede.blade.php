@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-start">
            <h6>Nueva sede</h6>
          </div>
          <div class="float-end">
          </div>
        </div>
        <hr>
        <form action="/sedes/crear" method="post">
          <label for="">Nombre de la sede:</label>
          <input type="text" class="form-control" name="sede" placeholder="Nombre de la sede">
          <hr>
          <input type="submit" class="btn btn-primary" value="Guardar">
        </form>
      </div>
    </div>
  </div>
@endsection
