@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	<p align="justify">
		Hola, apreciable <b>{{$cliente->isinscripcion->nombre_completo}}</b>, lamentamos comunicarte que tu solicitud de crédito fue <b>rechazada</b>.
	</p>
	<div class="">
		<label for="">Razón:</label>
		<p align="justify" style="background-color:#f2f2f2;font-weight:bold;margin:20px;padding:20px;">
			{{$razon}}
		</p>
	</div>
	<small>
			<center>
				Sí aún tienes dudas de la razón por la cual tu crédito fue rechazado, envia un mensaje de correo electrónico a <a href="mailto:credito@unisantorizaba.com">credito@unisantorizaba.com</a>.
			</center>
	</small>
@endsection
