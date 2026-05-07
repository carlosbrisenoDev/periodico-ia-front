@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
  <div class="col">
      <div class="card card-default large">
          <div class="card-body">
            <h3>Imagenes</h3>
            <div class="row">
              <div class="col-12">
                <form action="/imagenes/guardar" class="dropzone" id="dropzone">
                  <div class="fallback">
                    <input class="hidden" name="documento[]" type="file" multiple />
                  </div>
                </form>
              </div>
            </div>
            <br>
            <div class="alert alert-info hidden" role="alert">
              <i class="fa fa-info"></i>
              <a href="javascript:location.reload();">Recarga(F5)</a> para ver las imagenes cargadas en la lista o arrastra más imagenes
            </div>
            <br>
            <div class="row">
                <div class="col-2 hidden" style="display:gone;">
                  <div class="card topp">
                    <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="/imagenes/guardar">
                      <input  id="archivo"  type="file" style="display:none;" multiple name="documento[]" placeholder="Seleccione los documentos">
                      <div class="seleccionar">
                        <i class="fa fa-plus"></i>
                      </div>
                      <span class="texto">1. Agregar imagenes </span>
                      <div class="clearfix">
                        <button type="submit" class="btn btn-primary large" id="titulo">
                        <i class="fa fa-upload"></i>    2. Subir imagenes
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
                @if (count($imagenes) > 0)
                @foreach ($imagenes as $imagen)
                  <div class="col-2">
                    <div class="card topp">
                      <img src="/imagenes/watcharlittle/{{md5($imagen->id)}}" class="img img-responsive" alt="">
                      <span class="texto">{{str_replace("."," ",$imagen->titulo)}}</span>
                      <div class="row bg-secondary" style="margin:0;">

                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <a href="/imagenes/download/{{md5($imagen->id)}}" class="btn btn-secondary"><i class="fa fa-download"></i></a>
                              <a target="_blank" href="/imagenes/watchar/{{md5($imagen->id)}}" class="btn btn-secondary"><i class="fa fa-eye"></i></a>
                              <a href="/imagenes/delete/{{md5($imagen->id)}}" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                              <a href="/imagenes/fixTiny/{{md5($imagen->id)}}" class="btn btn-success"><i class="fas fa-screwdriver"></i></a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                @endforeach
                @else
                  <div class="col-2">
                    <div class="card topp">
                      <div class="img img-responsive text-center">
                        <div class="seleccionar">
                        </div>
                      </div>
                      <span class="texto">No hay imagenes</span>
                      <div class="row bg-secondary" style="margin:0;height:38px;">

                        <div class="col">
                          <div class="">

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              @endif
            </div>
          </div>
        </div>
      </div>
</div>
@endsection
@section('scripts')
  <script src="{{ asset('js/dropzone.js') }}"></script>
  <script type="text/javascript">
      $(document).ready(function(){
        $(".seleccionar").on("click",function(){
           $("#archivo").click();
        });
        $("#archivo").on("change",function(){
          $("#titulo").text("Subir "+$("#archivo").val().split('\\')[$("#archivo").val().split('\\').length-1]);
        });

        Dropzone.options.myAwesomeDropzone = false;
        Dropzone.autoDiscover = false

        var myDropzone = new Dropzone("#dropzone",{maxFilesize: 2,paramName: "documento[]"});
        $(".dz-message").text("Arrastra y suelta aquí tus imagenes para subirlas");
        myDropzone.on("addedfile", function(file) {
          $(".enviar").addClass("disabled");
        });
        myDropzone.on("addFile", function(file) {
          $(".alert").addClass("hidden");
        });
        myDropzone.on("success", function(file) {
          $(".alert").removeClass("hidden");
        });
      });
  </script>
@endsection
@section('styles')
  <link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
@endsection
