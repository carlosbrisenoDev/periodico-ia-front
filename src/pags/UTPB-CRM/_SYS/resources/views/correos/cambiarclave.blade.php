@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	Se ha cambiado la clave de acceso de tu cuenta {{$usuario->email}}.
	<div class="">
		<p align="justify">
			Información de acceso:
			<ul>
				<li>Usuario: <b>{{$usuario->email}}</b></li>
				<li>Contraseña:{{$clave}}</li>
			</ul>
		</p>
	</div>
@endsection
