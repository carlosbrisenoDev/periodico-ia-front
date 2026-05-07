@extends('correos.base')
@section('titulo')
	Solicitud
@endsection
@section('content')
	A continuación se anexa información acerca de las franquicias de Shirushi, sí desea afiliarse a Shirushi como franquiciatario,
	haga <a href="http://{{$_SERVER['HTTP_HOST']}}/franquiciatarios/solicitar/{{md5($franq->id)}}"> click aquí</a>, complete la información complementaria y espere por la llamada de uno de nuestros representantes.

	<div class="">
		<p align="justify">
			Presentación informativa:
			<a href="https://docs.google.com/presentation/d/1MD_hyCQnIxdFfD698QO_D04msWJe3SHApKwGZ5dMpS8/edit?usp=sharing">Presentación</a>
		</p>
	</div>

	<div class="">
		<p align="justify">
			Sí desea formar parte de las franquicias Shirushi haga click en el siguiente enlace.
			<a href="http://{{$_SERVER['HTTP_HOST']}}/franquiciatarios/solicitar/{{md5($franq->id)}}">Quiero formar parte de Shirushi</a>
		</p>
	</div>
@endsection
