@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	Su solicitud para ser empleado ha sido rechazado.
	<div class="">
		Razón:
		<p align="justify">
			{{$razon}}
		</p>
	</div>
	Gracias por su interés en ser parte de Shirushi.
@endsection
