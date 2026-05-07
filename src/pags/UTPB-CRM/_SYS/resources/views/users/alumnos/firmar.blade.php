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
          <h6 class="card-subtitle mb-2 text-muted">Firmar crédito</h6>
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
            <h5 class="card-title">Crédito</h5>
            <h6 class="card-subtitle mb-2 text-muted">Firmar crédito</h6>
            <hr>
            <video class="img-fluid" src="/video/{{$cartera->hasFirma->video_id}}" autoplay controls>
            </video>
          </div>
      @endif
    </div>
  </div>
  <div class="col">

  </div>
</div>

@endsection
@section('styles')
  <link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
@endsection
@section('scripts')
<script src="{{ asset('js/dropzone.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    @if (Auth::user()->cliente->ccredito()->status == 0)
    $(".as").bind("change",function(){
      $(this.parentNode).find("label").append("<i class='fas fa-cog fa-spin'></i>");
      $.post($(this).attr("w")+"?seto="+$(this).prop("name")+"&cid="+$(".cid").val()+"&v="+$(this).val(),function(data){
        $("label").find("i").remove()
      });
    });
    $(".rs").bind("click",function(){
      $(this.parentNode).find("label").append("<i class='fas fa-cog fa-spin'></i>");
      $.post($(this).attr("w")+"?seto="+$(this).prop("name")+"&cid="+$(".cid").val()+"&v="+$(this).val(),function(data){
        $("label").find("i").remove();
        location.reload();
      });
    });
    @endif
    var myDropzone = new Dropzone("#dropzone");
    $(".dz-message").text("Arrastra y suelta aquí archivos para adjuntarlos");
    myDropzone.on("success", function(file,data) {
      data = JSON.parse(data);
      if($(".table").find(".texto"))
        $(".table").empty();
      $(".table").append($("<tr>")
        .append(
          $("<td>")
            .css({"width":"200px"})
            .append($("<div>")
              .addClass("btn btn-link")
                .append($("<li>")
                  .addClass("fa fa-file"))))
          .append($("<td>").css({"line-height":"35px"}).text(data.titulo))
          .append($("<td>")));
    });
  });
</script>
@endsection
@section('styles')
  <style media="screen">
    hr{
      height:10px;
      background-color:#f6f6f6;
      border:0;
    }
    .line{
      height:2px;
      background-color:#f6f6f6;
      border:0;
      width:30%;
      margin:0;
      padding:0;
    }
    .text-muted{
      color:#BD773E !important;
    }
  </style>
@endsection
