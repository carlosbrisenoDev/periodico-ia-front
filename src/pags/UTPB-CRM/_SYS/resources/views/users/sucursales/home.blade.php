@extends('layouts.app')
@section('menu')
  <li>
      <a href="/home">
        <i class="fa fa-home"> Inicio</i>
      </a>
  </li>
  <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
        <i class="fas fa-wallet"></i>  Clientes <span class="caret"></span>
      </a>

      <ul class="dropdown-menu">

          <li>
              <a href="/{{Auth::user()->level->alias}}/pedidos">
                <i class="fas fa-list"></i> Todos
              </a>
          </li>
          <li>
              <a href="/{{Auth::user()->level->alias}}/saldo">
                <i class="fas fa-money"></i> Agregar saldo
              </a>
          </li>
      </ul>
  </li>
  @include('users.general')

@endsection
