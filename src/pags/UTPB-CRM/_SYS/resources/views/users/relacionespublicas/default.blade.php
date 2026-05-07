@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  @include('components.locked_module', ['moduleName' => 'Relaciones Públicas'])
@endsection
