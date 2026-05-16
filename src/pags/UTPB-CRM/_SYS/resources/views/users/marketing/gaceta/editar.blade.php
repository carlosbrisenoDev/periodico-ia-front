@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="col-md-12">
      <div class="card card-default">
          <div class="card-body">
          <form class=""  action="/gaceta/actualizar" method="post">
            <input type="submit" class="hidden correo">
            <input type="hidden" name="id" value="{{$pub->id}}">
            <div class="row">
              <div class="col-12">
                <label for="">Título:</label>
                <input type="text" class="form-control" value="{{$pub->titulo}}" name="titulo"  placeholder="Asombrosa noticia!">
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <label for="">Contenido</label>
                <textarea name="contenido" style="min-height:400px;" class="editor" id="editor" placeholder="Redacta y diseña">{{$pub->contenido}}</textarea>
              </div>
            </div>
          </form>
            <div class="row">
              <div class="col-12" id="drop">
                <form action="/gaceta/upload" class="dropzone" id="dropzone">
                  <div class="fallback">
                    <input name="file" type="file" multiple />
                  </div>
                </form>
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-12">
                <button type="button" class="btn btn-primary enviar float-right">
                  <i class="fa fa-send"></i> Guardar
                </button>
              </div>
            </div>
    </div>
  </div>
  </div>
@endsection
@section('scripts')
  <script src="{{ asset('js/dropzone.js') }}"></script>
  <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script type="text/javascript">
    var lista = [];
    $(document).ready(function() {
      tinymce.init({
        selector:'textarea',
        plugins: "image",
        image_list: lista
      });
      var myDropzone = new Dropzone("#dropzone");
      $(".dz-message").text("Arrastra y suelta aquí archivos para agregarlos a la lista de imagenes disponibles");
      myDropzone.on("addedfile", function(file) {
        $(".enviar").addClass("disabled");
      });
      myDropzone.on("complete", function(file) {
        $(".enviar").removeClass("disabled");
      });
      myDropzone.on("success", function(file,data) {
        lista.push({"title":data.split(",")[0],"value":"https://{{$_SERVER['HTTP_HOST']}}/gaceta/watchar/"+data.split(",")[1]});
      });
      $(".enviar").bind("click",function(){
        $(".correo").click();
      });
    });
  </script>
@endsection
@section('styles')
  <link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
@endsection
