@extends('layouts.app')
@section('menu')
  {{-- <li class="nav-item">
    <div class="dropdown">
      <a href="/" class="nav-link">
        Inicio
      </a>
    </div>
  </li>
  <li class="nav-item">
    <div class="dropdown">
      <a class="nav-link dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        Clientes
      </a>
      <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <li>
          <a href="/{{Auth::user()->level->alias}}/nuevo" class="dropdown-item border-radius-md">
            <span>Nuevo cliente</span>
          </a>
        </li>
        <li>
          <a href="/{{Auth::user()->level->alias}}/listar" class="dropdown-item border-radius-md">
            <span>Todos los clientes</span>
          </a>
        </li>
      </ul>
    </div>
  </li> --}}
  @include('components.header_menu')
  @include('users.general')
@endsection
