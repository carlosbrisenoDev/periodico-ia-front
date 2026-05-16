@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	Su solicitud para ser franquiciatario ha sido autorizada, esta acción le otorga su cuenta de acceso al sistema de información de usuarios
	<div class="">
		<p align="justify">
			Información de acceso:
			<ul>
				<li>Usuario: <b>{{$usuario->email}}</b></li>
				<li>Contraseña:{{$usuario->generarClave()}}</li>
				<li>URL de acceso: <a href="http://{{$_SERVER['HTTP_HOST']}}/login">http://{{$_SERVER['HTTP_HOST']}}/login</a> </li>
			</ul>
		</p>
	</div>
@endsection
