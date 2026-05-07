@extends('layouts.app')
@section('menu')
  <li>
      <a href="/home">
        <i class="fa fa-home"> Inicio</i>
      </a>
  </li>
    @include('users.general')
@endsection
