@extends('layouts.app')
@section('menu')
  <li>
      <a href="/home">
        <i class="fa fa-home"> Inicio</i>
      </a>
  </li>
  {{-- <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
        <i class="fa fa-user"></i>  Ciudadanos <span class="caret"></span>
      </a>

      <ul class="dropdown-menu">

          <li>
              <a href="/home">
                <i class="fa fa-plus"> Nuevo</i>
              </a>
          </li>
          <li>
              <a href="/{{Auth::user()->level->alias}}/buscar">
                <i class="fa fa-search"> Buscar</i>
              </a>
          </li>
      </ul>
  </li> --}}
  @include('users.general')
@endsection
