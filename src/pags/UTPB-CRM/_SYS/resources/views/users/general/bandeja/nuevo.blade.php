@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  @php
    $re = false;
    if(isset($_REQUEST["re"])){
      $box1 = (isset($_REQUEST["box"])) ? $_REQUEST["box"] : "INBOX";
      $rebox = (!strstr($box1,"INBOX")) ? "INBOX.$box1" : $box1;

      $mailbox = $con->getMailbox($rebox);
      $selection = $mailbox->getMails();
      $mail = $selection[$_REQUEST["re"]];
      $re = true;
      preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i",$mail->getHeader("from"), $matches);
      $email = $matches[0];
      preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i",$mail->getHeader("envelopeTo"), $matches);

      $cc = "";
      foreach($matches[0] as $val){
        $cc .= "$val,";
      }

    }
  @endphp
  <div class="col-md-12">
      <div class="card card-default">
          <div class="card-body">
          <form class=""  action="/bandeja/enviar" method="post">
            <input type="submit" class="hidden correo">
            <div class="row">
              <div class="col-12">
                <label for="">De:</label>
                <input type="text" class="form-control" value="{{Auth::user()->email}}" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <label for="">Para:</label>
                <input type="text" class="form-control" name="to" required placeholder="Destinatario,Destinatario,Destinatario" value="{{($re) ? $email[0] : ""}}{{(isset($_REQUEST["a"])) ? $_REQUEST["a"] : ""}}">
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-md-12 col-lg-6">
                <label for="">C.C:</label>
                <input type="text" class="form-control" name="cc" value="{{($re) ? $cc : ""}}" placeholder="Con copia para">
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label for="">C.C.O:</label>
                <input type="text" class="form-control" name="cco" placeholder="Con copia oculta">
                <input type="text" name="archivos" class="archivos hidden" value="">
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <label for="">Asunto:</label>
                <input type="text" class="form-control" value="{{($re) ? "RE: ".$mail->getHeader("subject") : ""}}" name="asunto" required placeholder="Asunto">
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <label for="">Mensaje</label>
                <textarea name="body" style="min-height:600px;" class="editor" id="editor"></textarea>
              </div>
            </div>
          </form>
          <form action="/usuarios/setsign" method="post">
            <div class="row">
              <div class="col-12">
                <label for="">Firma</label>
                <textarea name="sign" w="/usuarios/seto" style="min-height:400px;" class="editor2 as" id="editor2">{{Auth::user()->sign}}</textarea>
              </div>
            </div>
          </form>
            <div class="row">
              <div class="col-12" id="drop">
                <form action="/bandeja/upload" class="dropzone" id="dropzone">
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
                  <i class="fa fa-send"></i> Enviar
                </button>
              </div>
            </div>
            <input type="hidden" class="texto" value="{{($re) ? "[RE]: <hr>".$mail->getBody() : ""}}">
    </div>
  </div>
  </div>
@endsection
@section('scripts')
  <script src="{{ asset('js/dropzone.js') }}"></script>
  <script src="https://cdn.tiny.cloud/1/4eh5se8bzh2rwh4i26sh1a582xzigey103wfcd1h7smr5czs/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script type="text/javascript">
    function insert_contents(inst){
        inst.setContent($(".texto").val());
    }
    $(".as").bind("change",function(){
      $(this.parentNode).find("label").append("<i class='fas fa-cog fa-spin'></i>");
      $.post($(this).attr("w")+"?seto="+$(this).prop("name")+"&cid="+$(".cid").val()+"&v="+$(this).val(),function(data){
        $("label").find("i").remove()
      });
    });
    $(document).ready(function() {

      tinymce.init({
        selector: 'textarea',
        toolbar_mode: 'floating',
        language:"es_MX",
        plugins: 'image imagetools table link list preview save',
        tollbar:"save"
      });


      var myDropzone = new Dropzone("#dropzone");
      $(".dz-message").text("Arrastra y suelta aquí archivos para adjuntarlos");
      myDropzone.on("addedfile", function(file) {
        $(".enviar").addClass("disabled");
      });
      myDropzone.on("complete", function(file) {
        $(".enviar").removeClass("disabled");
      });
      myDropzone.on("success", function(file,data) {
        $(".archivos").val($(".archivos").val()+data+",")
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
