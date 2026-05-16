@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
  <div class="col">

  </div>
  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
    <div class="card">
      @php
        $cartera = \App\cartera::whereRAW("md5(id)='".$_REQUEST["cid"]."'")->first();
      @endphp
      @if ($cartera->hasFirma == null)
        <div class="card-body">
          <h5 class="card-title">{{$cartera->concepto}}</h5>
          <h6 class="card-subtitle mb-2 text-muted">Firmar crédito </h6>
          <hr>
          <div class="alert alert-info">
            <p align="justify">
              Para firmar tu crédito de estudiante y continuar con el beneficio, primero utiliza tu dispositivo celular para escanear el siguiente código QR.
            </p>
          </div>
          <hr>
          <img class="img-fluid" src="https://chart.googleapis.com/chart?chs=530x530&cht=qr&chl={{urlencode("https://sii.unisantorizaba.com/signature?u=".md5($cartera->id))}}&choe=UTF-8">
          <hr>
          <div class="text-center">
            <small>¿No puedes leer el código?, <a href="https://sii.unisantorizaba.com/signature?u={{md5($cartera->id)}}">realiza el proceso en tu computadora</a>, necesitaras cámara y microfono.</small>
          </div>
        </div>
        @else
          <div class="card-body">
            <h5 class="card-title">{{$cartera->concepto}}</h5>
            <h6 class="card-subtitle mb-2 text-muted">Firmar crédito</h6>
            <hr>
            <video class="img-fluid" src="/video/{{$cartera->hasFirma->video_id}}" autoplay controls>
            </video>
            <hr>
            @include('componentes.texto_firma')
            <hr>
            <a href="/cartera/aceptarfirma/yes?cid={{md5($cartera->id)}}" class="btn btn-success">Aceptar firma</a>
            <a href="/cartera/rechazarfirma/no?cid={{md5($cartera->id)}}" class="btn btn-danger">Rechazar firma</a>
          </div>
      @endif
    </div>
  </div>
  <div class="col">

  </div>
</div>
@endsection
