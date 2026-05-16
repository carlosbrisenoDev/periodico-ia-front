@extends('correos.base')
@section('titulo')
	Requerimiento de pago
@endsection
@section('content')
	<p align="justify">
		Hola, apreciable <b>{{$pago->tabla->cliente->isinscripcion->nombre_completo}}</b>, por medio del presente se notifica que tiene un pago pendiente correspondiente con el mes de <b>1/{{$pago->mes}}/{{$pago->anio}}</b> por un monto de <b>{{$pago->pago}} ({{\App\Http\Controllers\num2letras::convert(str_replace("$","",str_replace(",","",$pago->pago)))}})</b>.
	</p>
	<p>
		Te invitamos a realizar tu pago en la brevedad posible.
	</p>
	<b>Información de pago:</b>
	<table>
		<tr>
			<td><b>TITULAR:</td>
			<td>CORPORATIVO UNISANT, S.C.</td>
		</tr>
		<tr>
			<td><b>BANCO:</b></td>
			<td>SCOTIABANK</td>
		</tr>
		<tr>
			<td><b>NO. DE CUENTA:</b></td>
			<td>00106626893</td>
		</tr>
		<tr>
			<td><b>NO. CLABE INTERBANCARIA:</b></td>
			<td>044180001066268936</td>
		</tr>
		<tr>
			<td><b>SUCURSAL:</b></td>
			<td>105</td>
		</tr>
		<tr>
			<td><b>SEDE:</b></td>
			<td>CDMX</td>
		</tr>
	</table>
	<p>
		Es importante que una vez realizado el pago mensual, envies la información y comprobantes al departamento de crédito (<a href="mailto:credito@unisantorizaba.com">credito@unisantorizaba.com</a>) para su posterior aplicación y conocimiento.
		Si ya has realizado el pago, por favor, hacer caso omiso a este mensaje.
	</p>
	<p>
		<b>IMPORTANTE:</b></br>
		Puedes encontrar información en tu cuenta de control escolar ({{$pago->tabla->cliente->usuario->email}}) proporcionada por tu asesor en <a href="https://sii.unisantorizaba.com/">https://sii.unisantorizaba.com/</a>.
	</p>
	<br>
<small>
		<center>
			Sí aún tienes dudas adicionales, envia un mensaje de correo electrónico a <a href="mailto:credito@unisantorizaba.com">credito@unisantorizaba.com</a>.
		</center>
</small>
@endsection
