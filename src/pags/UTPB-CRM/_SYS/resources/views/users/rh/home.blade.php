@extends('layouts.app')
@section('menu')
  @include('components.header_menu')
@endsection

@section('content')
  @include('components.locked_module', ['moduleName' => 'Recursos Humanos'])
@endsection
