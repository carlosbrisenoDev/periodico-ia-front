@extends('layouts.gruposhirushi')
@section('content')
  <form method="post" action="/g/quejas/aplicar">
    <div class="jumbotron">
      <p>
        Gracias por tus quejas y/o sugerencias, te recordamos que:
      </p>
      <p align="justify">
        <b>Tu queja es confidencial.</b>
      </p>
      <p>
        La información proporcionada será evaluada por el área correspondiente.
      </p>
    </div>
    <div class="form-group row">
      <label for="inputnombre" class="col-sm-2 col-form-label">Nombre completo</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="nombre" required id="inputnombre" placeholder="Nombre completo">
      </div>
    </div>
    <div class="form-group row">
      <label for="inputEmail3" class="col-sm-2 col-form-label">Correo electrónico</label>
      <div class="col-sm-10">
        <input type="email" class="form-control" required name="correo" id="inputEmail3" placeholder="ejemplo@algo.com">
      </div>
    </div>
    <div class="form-group row">
      <label for="inputEmail3" class="col-sm-2 col-form-label">Celular</label>
      <div class="col-sm-10">
        <input type="numeric" class="form-control" required name="tel" id="inputEmail3" placeholder="222 888 88 99">
      </div>
    </div>
    <fieldset class="form-group">
      <div class="row">
        <legend class="col-form-label col-sm-2 pt-0">Seriedad de la queja o sugerencia</legend>
        <div class="col-sm-10">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="severidad" id="gridRadios1" value="0" checked>
            <label class="form-check-label" for="gridRadios1">
              Leve
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="severidad" id="gridRadios2" value="1">
            <label class="form-check-label" for="gridRadios2">
              Regular
            </label>
          </div>
          <div class="form-check disabled">
            <input class="form-check-input" type="radio" name="severidad" id="gridRadios3" value="2">
            <label class="form-check-label" for="gridRadios3">
              Alta
            </label>
          </div>
        </div>
      </div>
    </fieldset>
    <div class="form-group row">
      <label for="inputEmail4" class="col-sm-2 col-form-label">Describe lo ocurrido:</label>
      <div class="col-sm-10">
        <textarea class="form-control" required name="descripcion" style="height:100px;" id="inputEmail4" placeholder="..."></textarea>
      </div>
    </div>
    <input type="hidden" name="archivos" class="archivos" value="">
    <input type="submit" class="vs" style="display:none;" name="" value="">
  </form>
  <div class="form-group row">
    <label for="inputEmail4" class="col-form-label">¿Tienes fotos o elementos adicionales?, Adjuntalos:</label>
  </div>
    <div class="row">
      <div class="col-12">
        <form action="/quejas/upload" id="dropy" class="dropzone" style="border:none;border:dotted #333 2px;">
          <div class="fallback">
            <input name="file" type="file" multiple />
          </div>
        </form>
      </div>
    </div>
    <br><br>
    <div class="form-group row">
      <div class="col-sm-10">
        <button type="button" class="btn btn-primary">Enviar al corporativo</button>
      </div>
    </div>
@endsection
@section('scripts')
  <script src="{{ asset('js/dropzone.js') }}"></script>
  <script type="text/javascript">
      Dropzone.options.myAwesomeDropzone = false;
      Dropzone.autoDiscover = false;

      $(function(){
        $(".btn").bind("click",function(){
          $(".vs").click();
        });

        var myDropzone = new Dropzone("#dropy",{maxFilesize: 2,paramName: "file"});

        $(".dz-message").text("Arrastra y suelta aquí archivos para agregarlos al envio");
        myDropzone.on("addedfile", function(file) {
          $(".enviar").addClass("disabled");
        });
        myDropzone.on("complete", function(file) {
          $(".enviar").removeClass("disabled");
        });
        myDropzone.on("success", function(file,data) {
          $(".archivos").val($(".archivos").val()+data+",");
        });
      });
  </script>
@endsection
@section('styles')
  <link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
@endsection
