@extends('layouts.app')
@section('menu')
  {{-- <li>
      <a href="/home">
        <i class="fa fa-home"></i> Inicio
      </a>
  </li>
  @if (Auth::user()->empleado)
    @if(Auth::user()->empleado->status == 5)
      <li>
          <a href="/home">
            <i class="fas fa-newspaper"></i> Noticias
          </a>
      </li>
    @endif
  @endif --}}
  @include('components.header_menu')
@endsection
