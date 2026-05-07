@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
    <div class="col-2">

    </div>
    <div class="col">
      <div class="card">
        <div class="card-body table-responsive" style="font-family:none;font-weight:normal;">
          @php
            $c = $empleado = Auth::user()->empleado;
            $formatterES = new NumberFormatter("es-MX", NumberFormatter::SPELLOUT);
          @endphp
          <table class="table">
            <tr>
              <td class="text-center">PAGARÉ</td>
            </tr>
            <tr>
              <td>FECHA DE VENCIMIENTO:</td>
            </tr>
          </table>
          <p class="justify" style="font-family:none;font-weight:normal;">
            Por este pagaré prometo pagar incondicionalmente a la orden de
            <b>EDUCACIÓN A DISTANCIA UNIVERSIDAD SANTANDER, S.A. DE C.V. SOFOM E.N.R.</b> la cantidad de
            <b>$145</b> (<b>{{strtoupper($formatterES->format(145))}}</b> 00/100 M.N.) el día <b>{{date("d")}}</b> del mes de <b>{{date("M")}}</b> del año
            <b>{{date("Y")}}</b>, en su domicilio ubicado en la calle
             <b>{{$c->cocredito->direccion}}</b> Número <b>{{$c->cocredito->numero}}</b> en la Colonia
            <b>{{$c->cocredito->col}}</b> en la Ciudad de <b>{{$c->cocredito->municipio}}</b>. En caso de
            controversia y ejecución el suscriptor de éste pagaré y su aval renuncian al fuero que
            por razón de Territorio les corresponda o les pudiera corresponder, sometiéndose
            expresamente a los Tribunales Competentes de la Ciudad de Orizaba, Veracruz.
          </p>
          <p class="justify" style="text-indent:20px;">
            En caso de que este pagaré no sea pagado en la fecha de su vencimiento, su
importe causará intereses moratorios a razón del 12% anual desde la fecha de su
vencimiento y hasta la fecha de pago total del mismo, sin que por ello se considere
prorrogada la obligación principal.
          </p>
          <p>
            <h4>FECHA Y LUGAR DE SUSCRIPCIÓN:</h4>
          </p>
          <p>
Orizaba, Veracruz, el día <b>{{date("d")}}</b> del mes de <b>{{date("M")}}</b> del año
<b>{{date("Y")}}</b>.
          </p>
          <p>
            <h4>SUSCRIPTOR</h4>
          </p>
          <p>
            <label for="">
              NOMBRE: {{$c->cocredito->nombre}}
            </label></br>
            <label for="">
              DOMICILIO: {{$c->cocredito->direccion}}, {{$c->cocredito->col}}, {{$c->cocredito->cp}}
            </label></br>
            <label for="">
              CIUDAD: {{$c->cocredito->municipio}}
            </label></br>
            <label for="">
              FIRMA: {{$c->cocredito->nombre}}
            </label>
          </p>
          <p>
            <h4>AVAL</h4>
          </p>
          <p>
            <label for="">
              NOMBRE: {{$c->cocredito->nombre_s}}
            </label></br>
            <label for="">
              DOMICILIO: {{$c->cocredito->direccion_s}}, {{$c->cocredito->col_s}}, {{$c->cocredito->cp_s}}
            </label></br>
            <label for="">
              CIUDAD: {{$c->cocredito->municipio_s}}
            </label></br>
            <label for="">
              FIRMA: {{$c->cocredito->nombre_s}}
            </label>
          </p>

      </div>
    </div>
  </div>
    <div class="col-2">

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
