@extends('correos.base')
@section('titulo')
	Video Firma
@endsection
@section('content')
	<p align="justify">
		Hola, apreciable <b>{{$cartera->cliente->isinscripcion->nombre_completo}}</b>, lamentamos informarte que tu Video Firma filmada para tu crédito <b>{{$cartera->concepto}}</b> fue rechazada, por favor, intenta nuevamente en la plataforma de control escolar o <a href="/signature?u={{md5($cartera->id)}}">utilizando este enlace</a>, si tienes dudas, comunicate con el departamento de crédito.
	</p>
	<p>
		<b>IMPORTANTE:</b></br>
		Puedes encontrar información en tu cuenta de control escolar ({{$cartera->cliente->usuario->email}}) proporcionada por tu asesor en <a href="https://sii.unisantorizaba.com/">https://sii.unisantorizaba.com/</a>.
	</p>
		<br>
	<small>
			<center>
				Sí aún tienes dudas adicionales, envia un mensaje de correo electrónico a <a href="mailto:credito@unisantorizaba.com">credito@unisantorizaba.com</a>.
			</center>
	</small>
@endsection
