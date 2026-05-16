@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">

                    <h3><a href="/ciudadano/modify/{{md5($ciudadano->id)}}">{{$ciudadano->full_name()}}</a></h3>
                    <h4>Credencial ciudadana</h4>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/tarjeta/refresh">
                      <input type="hidden" name="id" value="{{$ciudadano->id}}">
                      <input type="hidden" name="imagen" id="imagen" value="">
                      {{ csrf_field() }}
                    <div class="col-md-12">
                      <div class="col-md-6">
                        <h3>Fotografía del ciudadano</h3>
                            <div class="col-md-6">
                              <h4>Cámara</h4>
                              <canvas id="canvas"  width="232" height="330"></canvas>
                            </div>
                            <div class="col-md-6">
                              <h4>Fotografía actual</h4>
                              <canvas id="canvas2" class="thumbnail" width="232" height="330"></canvas>
                            </div>
                      </div>
                      <div class="col-md-6">
                        <video id="video" width="640" height="480" autoplay class="hidden"></video>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <hr>
                      <div class="col-md-12">
                        <div class="col-md-3">
                        <input type="checkbox" {{(($ciudadano->sicargo == "1")) ? "checked" : ""}} class="empleado" id="empleado" name="empleado" value="1">
                        <label for="empleado">Imprimir tarjeta de empleado</label>
                        </div>
                        <div class="col-md-6">
                          <div class="col-md-12 {{(($ciudadano->sicargo != "1")) ? "hidden" : ""}} depa">
                            <label for="cargo">Cargo:</label>
                            <input type="text" name="cargo" autocomplete="off" class="cargo form-control" value="{{$ciudadano->cargo}}" placeholder="Cargo (23 caracteres)">
                            <span class="estato" style="color:red;text-indent:10px;font-size:4mm;">35</span> Caracteres restantes
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <hr>
                      <div class="col-md-3">
                        <a href="#" class="btn btn-success large snap">
                        <i class="fa fa-camera"></i>    1. Tomar fotografía
                      </a>
                      </div>
                        <div class="col-md-3">
                          <button type="submit" class="btn btn-primary large">
                          <i class="fa fa-refresh"></i>    2. Actualizar
                          </button>
                        </form>
                        </div>

                        <div class="col-md-3">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$ciudadano->id}}">
                            <a target="_blank" href="/tarjeta/generar/{{md5($ciudadano->id)}}" class="btn btn-info large">
                            <i class="fa fa-address-card"></i>  3. Ver credencial
                          </a>
                        </div>
                        <div class="col-md-3">

                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
  <script type="text/javascript">
  var e = ({{$ciudadano->sicargo}} == "1") ? true : false;
  $(function(){
    $(".cargo").bind("keyup",function(){
      $(this).val($(this).val().substring(0,35));
      $(".estato").text(35-$(this).val().length);
    });
    $(".empleado").bind("click",function(){
      if(!e)
      {
        $(".depa").removeClass("hidden");
        e = true;
      } else
      {
        $(".depa").addClass("hidden");
        e = false;
      }
    });
  });
    var img = new Image();
    img.onload = function(){
      document.getElementById('canvas2').getContext("2d").drawImage(img,0,0);
      $("#imagen").val(document.getElementById('canvas2').toDataURL("image/png"));
    }
    @php
      if(!file_exists(storage_path()."/perfil/".md5($ciudadano->id).".png"))
      {
        $i = "/images/perfil.jpg";
      } else {
        $i = \Image::make(storage_path()."/perfil/".md5($ciudadano->id).".png")->resize(232,330)->encode("data-url");
      }
    @endphp
    img.src = "{{$i}}";
  var video = document.getElementById("video");
  if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
        video.src = window.URL.createObjectURL(stream);
        video.play();
    });
  }
    setInterval(function(){
      document.getElementById('canvas').getContext("2d").drawImage(video,204,75,236,405,0,0,232,330);
    },50);
  </script>
@endsection
