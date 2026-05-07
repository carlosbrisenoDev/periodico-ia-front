@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
  <div class="col">
      <div class="card card-default large">
          <div class="card-body">
            <div class="clearfix">
              <div class="pull-left">
                <h3>Nueva categoría</h3>
              </div>
            </div>
            <div class="row">
              <div class="col">
                Nombre de la categoria
              </div>
            </div>
          </div>
        </div>
      </div>
</div>
@endsection
@section('scripts')

@endsection
