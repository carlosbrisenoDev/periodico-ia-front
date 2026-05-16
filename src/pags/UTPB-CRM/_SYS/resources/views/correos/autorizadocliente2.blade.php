@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	Su cuenta de inscripción esta lista.
	<div class="">
		<p align="justify">
			Información de acceso:
			<ul>
				<li>Usuario: <b>{{$usuario->email}}</b></li>
				<li>Contraseña:{{$usuario->generarClave}}</li>
				<li>URL de acceso: <a href="http://{{$_SERVER['HTTP_HOST']}}">http://{{$_SERVER['HTTP_HOST']}}</a> </li>

			</ul>
		</p>
	</div>
@endsection
