@extends('correos.base')
@section('titulo')
	Nuevo Asesor
@endsection
@section('content')
	<p align="justify">
		Hola, <b>{{$user->name}}</b> tienes un nuevo cliente asignado a ti.
        <small>{{$cliente->nombre}}</small>
        <small>{{$cliente->correo}}</small>
        <small>{{$cliente->telefono}}</small>
        
	</p>
	<p>
		<b>Siguiente paso:</b></br> Entra a la plataforma para darle seguimiento.
	</p>
    
    {{-- <a href="{{url('/reporte/'.md5($reporte->id))}}" class="btn btn-info">Ver el Reporte</a> --}}
@endsection
