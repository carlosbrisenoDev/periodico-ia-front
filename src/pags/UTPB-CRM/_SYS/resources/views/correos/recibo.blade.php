@extends('correos.base2')
@section('titulo')
	Recibo de pago
@endsection
@section('content')
	<p align="justify">
		Hola, apreciable <b>{{$pago->tabla->cliente->isinscripcion->nombre_completo}}</b>, aquí esta tu recibo de pago del mes de {{$pago->mes}}/{{$pago->anio}}.
	</p>
	<p>
		Aclaraciones: departamento de crédito (<a href="mailto:credito@unisantorizaba.com">credito@unisantorizaba.com</a>) para su posterior aplicación y conocimiento.
	</p>

		<div class="recibo" style="padding:20px;height:auto;border:solid #ccc 1px;">
			<div style="text-align:center;margin-top:30px;margin-bottom:30px;">
				<img src="https://{{$_SERVER['HTTP_HOST']}}/images/logo.png" width="60%" height="auto" alt="Unisant Orizaba">
			</div>
			<div style="text-align:center">
				<h2>RECIBO DE PAGO</h2>
			</div>
			<table align="center">
				<tbody>
					<tr>
						<td>
							<b>
								Folio:
							</b>
						</td>
						<td>
								{{$pago->id}}
						</td>
					</tr>
					<tr>
						<td>
							<b>
								Num Pago:
							</b>
						</td>
						<td>
								{{$pago->numero}}
						</td>
					</tr>
					<tr>
						<td>
							<b>
								Fecha:
							</b>
						</td>
						<td>
								{{$pago->updated_at}}
						</td>
					</tr>
					<tr>
						<td>
							<b>
								Crédito:
							</b>
						</td>
						<td>
								CRUOV-{{\carbon\carbon::parse($pago->tabla->cliente->created_at)->format("Y")}}-{{$pago->tabla->cliente->id}}
						</td>
					</tr>
					<tr>
						<td>
							<b>
								Cliente:
							</b>
						</td>
						<td>
								UOV-{{\carbon\carbon::parse($pago->tabla->cliente->created_at)->format("Y")}}-{{$pago->tabla->cliente->id}} - {{strtoupper($pago->tabla->cliente->isinscripcion->nombre_completo)}}
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<br>
						</td>
					</tr>
					<tr>
						<td>
							<b>
								Abono a Capital:
							</b>
						</td>
						<td>
							{{$pago->capital}}
						</td>
					</tr>
					<tr>
						<td>
							<b>
								Intereses:
							</b>
						</td>
						<td>
							{{$pago->interes}}
						</td>
					</tr>
					<tr>
						<td>
							<b>
								IVA:
							</b>
						</td>
						<td>
							0.00
						</td>
					</tr>
					<tr>
						<td>
							<b>
								Moratorios:
							</b>
						</td>
						<td>
							0.00
						</td>
					</tr>
					<tr>
						<td>
							<b>
								IVA:
							</b>
						</td>
						<td>
							0.00
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<hr style="border-style:dotted;">
						</td>
					</tr>
					<tr>
						<td>
							<b>
								Total:
							</b>
						</td>
						<td>
							{{$pago->pago}}
						</td>
					</tr>
					<tr>
						<td>
							<b>
								Saldo actual:
							</b>
						</td>
						<td>
							@php
								$acumulado = str_replace("$","",str_replace(",","",$pago->acumulado));
							@endphp
							${{number_format($pago->tabla->cartera->total-$acumulado,2,",",".")}}
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;">
							({{\App\Http\Controllers\num2letras::convert(str_replace("$","",str_replace(",","",$pago->pago)))}})
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<br><br>
		<p>
			<b>IMPORTANTE:</b></br>
			Puedes encontrar información en tu cuenta de control escolar ({{$pago->tabla->cliente->usuario->email}}) proporcionada por tu asesor en <a href="https://sii.unisantorizaba.com/">https://sii.unisantorizaba.com/</a>. Este recibo no es un comprobante fiscal.
		</p>
		<br>
	<small>
			<center>
				Sí aún tienes dudas adicionales, envia un mensaje de correo electrónico a <a href="mailto:credito@unisantorizaba.com">credito@unisantorizaba.com</a>.
			</center>
	</small>
@endsection
