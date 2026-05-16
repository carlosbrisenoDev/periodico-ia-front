@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	<p align="justify">
		Hola, apreciable <b>{{$tabla->cliente->isinscripcion->nombre_completo}}</b>, nos complace entregarte tu tabla de pagos.
	</p>
	<p>
		TODOS los comprobantes de pago efectuados deberán ser enviados al departamento de crédito (<a href="mailto:credito@unisantorizaba.com">credito@unisantorizaba.com</a>) para su posterior aplicación y conocimiento.
	</p>
		<table style="width:100%;">
			<thead style="background:#343A40;color:white;text-align:center;">
				<th>No. pago</th>
				<th>Año</th>
				<th>Mes</th>
				<th>Acumulado</th>
				<th>Pago</th>
				<th>Capital</th>
				<th>Interes</th>
			</thead>
			<tbody>
				@foreach ($tabla->pagos as $pago)
					<tr>
						<td style="padding:.75rem;vertical-align: top;border-top: 1px solid #dee2e6;">{{$pago->numero}}</td>
						<td style="padding:.75rem;vertical-align: top;border-top: 1px solid #dee2e6;">{{$pago->anio}}</td>
						<td style="padding:.75rem;vertical-align: top;border-top: 1px solid #dee2e6;">1 {{$pago->mes}}</td>
						<td style="padding:.75rem;vertical-align: top;border-top: 1px solid #dee2e6;">{{$pago->acumulado}}</td>
						<td style="padding:.75rem;vertical-align: top;border-top: 1px solid #dee2e6;">{{$pago->pago}}</td>
						<td style="padding:.75rem;vertical-align: top;border-top: 1px solid #dee2e6;">{{$pago->capital}}</td>
						<td style="padding:.75rem;vertical-align: top;border-top: 1px solid #dee2e6;">{{$pago->interes}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		<br>
		<br>
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
		<br><br>
		<p>
			<b>IMPORTANTE:</b></br>
			Puedes encontrar información en tu cuenta de control escolar ({{$tabla->cliente->usuario->email}}) proporcionada por tu asesor en <a href="https://sii.unisantorizaba.com/">https://sii.unisantorizaba.com/</a>.
		</p>
		<br>
	<small>
			<center>
				Sí aún tienes dudas adicionales, envia un mensaje de correo electrónico a <a href="mailto:credito@unisantorizaba.com">credito@unisantorizaba.com</a>.
			</center>
	</small>
@endsection
