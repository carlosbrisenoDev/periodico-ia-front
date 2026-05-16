@extends('correos.base')
@section('titulo')
	Conforma tu dirección electrónica
@endsection
@section('content')
		<div class="">
		<p align="justify">
			Confirma tu dirección de correo electrónico mediante el siguiente enlace:
			<a href="http://{{$_SERVER['HTTP_HOST']}}/usuarios/confirmar/{{md5($cliente->id)}}">Confirmar dirección</a>
		</p>
	</div>
@endsection
