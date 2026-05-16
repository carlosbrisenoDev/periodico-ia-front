@extends('correos.base')
@section('titulo')
	Nuevo Reporte
@endsection
@section('content')
	<p align="justify">
		Hola, <b>{{$user->name}}</b>, {{$creador->name}} creo el reporte {{$reporte->titulo}} y te lo ha asignado..
	</p>
	<p>
		<b>Siguiente paso:</b></br> Entra a la plataforma para darle seguimiento.
	</p>
    
    <a href="{{url('/reporte/'.md5($reporte->id))}}" class="btn btn-info">Ver el Reporte</a>
@endsection
