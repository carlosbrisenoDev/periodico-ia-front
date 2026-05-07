@extends('correos.base')
@section('titulo')
	Reasignación de Asesor
@endsection
@section('content')
	<p align="justify">
		Hola, <b>{{$user->name}}</b> tu cliente "{{$cliente->nombre}}", tiene un nuevo asesor. Por lo que no es necesario darle seguimiento.
	</p>
	<p>
		{{-- <b>Siguiente paso:</b></br> Entra a la plataforma para darle seguimiento. --}}
	</p>
    
    {{-- <a href="{{url('/reporte/'.md5($reporte->id))}}" class="btn btn-info">Ver el Reporte</a> --}}
@endsection
