@extends('layouts.app')
@section('menu')
  {{-- <li>
      <a href="/home">
        <i class="fa fa-home"> Inicio</i>
      </a>
  </li>
  <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
        <i class="fas fa-credit-card"></i>  Clientes <span class="caret"></span>
      </a>

      <ul class="dropdown-menu">
          <li>
              <a href="/{{Auth::user()->level->alias}}/notify">
                <i class="fas fa-file-invoice-dollar"></i> Pagos atrasados
              </a>
          </li>
          <li>
              <a href="/{{Auth::user()->level->alias}}/noventa">
                <i class="fas fa-exclamation-circle text-danger"></i> 60 Días o más
              </a>
          </li>
          <li>
              <a href="/{{Auth::user()->level->alias}}/pagos">
                <i class="fas fa-file-invoice"></i> Pagos {{Date("M")}}
              </a>
          </li>
          <li>
              <a href="/{{Auth::user()->level->alias}}/listar">
                <i class="fas fa-users"></i> Clientes
              </a>
          </li>
      </ul>
  </li>
  <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
        <i class="fas fa-cubes"></i>  Materiass <span class="caret"></span>
      </a>

      <ul class="dropdown-menu">
          <li>
              <a href="/{{Auth::user()->level->alias}}/materias">
                <i class="fas fa-cubes"></i>   Materias en curso
              </a>
          </li>
      </ul>
  </li> --}}
  @include('components.header_menu')
  @include('users.general')
@endsection
