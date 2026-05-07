@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-heading">card de control</div>

                <div class="card-body">
                    <h3 class="success">Operación exitosa</h3>
                </div>
            </div>
        </div>
@endsection
