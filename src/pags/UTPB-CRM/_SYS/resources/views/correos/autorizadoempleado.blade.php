@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	Su solicitud para ser empelado ha sido autorizada, le ha sido otorgada una cuenta de acceso al sistema de información de usuarios; dentro del sistema añada los documentos necesarios y complete la información de su trayectoria como trabajador.
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
