@extends('layouts.app')
@section('menu')
  <li>
      <a href="/home">
        <i class="fa fa-home"> Inicio</i>
      </a>
  </li>
  <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
        <i class="fa fa-file-text"></i>  Folios <span class="caret"></span>
      </a>

      <ul class="dropdown-menu">
          <li>
              <a href="/{{Auth::user()->level->alias}}/ft">
                <i class="fa fa-file"> Ficha técnica</i>
              </a>
          </li>
          <li>
              <a href="/{{Auth::user()->level->alias}}/oficios">
                <i class="fa fa-file-o"> Oficios</i>
              </a>
          </li>
      </ul>
  </li>
    @include('users.general')
@endsection
