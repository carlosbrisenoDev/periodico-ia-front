@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	<p align="justify">
		Hola, apreciable <b>{{$cliente->isinscripcion->nombre_completo}}</b>, nos complace comunicarte que tu solicitud de crédito fue <b>aprobada</b>.
	</p>
	<p>
		<b>Siguiente paso:</b></br> Entra a la plataforma en linea de Unisant Orizaba con tu correo y clave proporcionados con anterioridad, una vez dentro, selecciona el nuevo apartado disponible en tu cuenta "Ver mi tabla de pagos".
	</p>
	<div class="">
		<label for="">Notas del departamento de crédito:</label>
		<p align="justify" style="background-color:#f2f2f2;font-weight:bold;margin:20px;padding:20px;">
			{{$razon}}
		</p>
	</div>
	<small>
			<center>
				Sí aún tienes dudas adicionales, envia un mensaje de correo electrónico a <a href="mailto:credito@unisantorizaba.com">credito@unisantorizaba.com</a>.
			</center>
	</small>
@endsection
