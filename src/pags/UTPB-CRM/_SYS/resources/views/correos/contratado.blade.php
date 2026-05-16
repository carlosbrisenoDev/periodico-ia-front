@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	Hola {{$empleado->nombre}}, tu solicitud ha sido marcada como Contratado, por lo cual tu cuenta de empleado a cambiado y ahora tienes acceso
	a más funcionalidades.
	<div class="">
		<p align="justify">
			Bienvenido al equipo de Shirushi.
		</p>
	</div>
@endsection
