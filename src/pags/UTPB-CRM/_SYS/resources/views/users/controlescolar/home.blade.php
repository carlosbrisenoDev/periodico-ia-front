@extends('layouts.app')
@section('menu')
  {{-- <li>
      <a href="/home">
        <i class="fa fa-home"> Inicio</i>
      </a>
  </li>
  <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
        <i class="fas fa-users"></i> Ventas <span class="caret"></span>
      </a>

      <ul class="dropdown-menu">
          <li>
              <a href="/ventas/nuevo">
                <i class="fa fa-file"></i> Nuevo cliente
              </a>
          </li>
          <li>
              <a href="/ventas/listar">
                <i class="fas fa-list"></i> Todos los clientes
              </a>
          </li>
      </ul>
  </li>
  <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
        <i class="fas fa-users"></i> Marketing <span class="caret"></span>
      </a>

      <ul class="dropdown-menu">
          <li>
              <a href="/ventas/gacetadeenvio">
                <i class="fa fa-envelope"></i> Gaceta de envios
              </a>
          </li>
      </ul>
  </li>
  <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
        <i class="fas fa-chalkboard-teacher"></i> Control escolar <span class="caret"></span>
      </a>

      <ul class="dropdown-menu">
          <li>
              <a href="/ventas/alumnos">
                <i class="fa fa-file"></i> Alumnos ({{\Carbon\carbon::now()->format("M Y")}})
              </a>
          </li>
          <li>
              <a href="/ventas/calendario">
                <i class="fa fa-calendar"></i> Cal ({{\Carbon\carbon::now()->format("M Y")}})
              </a>
          </li>
          <li>
              <a href="/ventas/facturas">
                <i class="fas fa-file-invoice"></i> Facturación
              </a>
          </li>
          <li>
              <a href="/ventas/notify">
                <i class="fas fa-flag"></i> Notificar alumnos
              </a>
          </li>
          <li>
              <a href="/controlescolar/upload">
                <i class="fas fa-upload"></i> Subir materias
              </a>
          </li>
      </ul>
  </li>
  <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
        <i class="fas fa-cubes"></i>  Materias <span class="caret"></span>
      </a>

      <ul class="dropdown-menu">
          <li>
              <a href="/creditos/materias">
                <i class="fas fa-cubes"></i>   Materias en curso
              </a>
          </li>
      </ul>
  </li>
  <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
        <i class="fas fa-headset"></i> Call Center <span class="caret"></span>
      </a>

      <ul class="dropdown-menu">
          <li>
              <a href="/ventas/rollcenter">
                <i class="fas fa-phone-volume"></i> Llamar clientes
              </a>
          </li>
          <li>
              <a href="/ventas/listarcenter">
                <i class="fas fa-address-book"></i> Todos los clientes
              </a>
          </li>
      </ul>
  </li> --}}
  @include('components.header_menu')
    @include('users.general')
@endsection
