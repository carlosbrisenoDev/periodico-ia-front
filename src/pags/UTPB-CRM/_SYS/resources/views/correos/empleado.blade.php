@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	A continuación se anexa un enlace para confirmar y concluir tu registro como solititante de empleado en Shirushi.
	<div class="">
		<p align="justify">
			Enlace:
			<a href="http://{{$_SERVER['HTTP_HOST']}}/empleados/solicitar/{{md5($empleado->id)}}">Quiero formar parte de Shirushi</a>
		</p>
	</div>
@endsection
