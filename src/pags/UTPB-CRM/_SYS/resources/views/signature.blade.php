<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <title>Firma de crédito estudiantil</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.2/moment.min.js"></script>
    <style media="screen">
      .camera{
        width:100%;
        height:auto;
      }
      .camera-roll{
        margin-left: -15px;
        margin-right: -15px;
      }
      .logo{
        position: absolute;
        width:20%;
        height:auto;
        right:10px;
        top:20px;
      }
    </style>
  </head>
  <body>
    <div class="modal" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Instrucciones:</h5>
          </div>
          <div class="modal-body">
            <p>
              Leé el parrafo detallado abajo, cuando estes list@, presiona el botón "Comenzar a grabar" y leé nuevamente en voz alta; asegurate de que tu rostro sea visible, no uses gorras, sombreros o elementos que puedan obstruir tu rostro.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="$('#staticBackdrop').toggle();$('.modal-backdrop').hide();" data-bs-dismiss="modal">Entendido</button>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col">
          @php
            $cartera = \App\cartera::whereRAW("md5(id)='".$_REQUEST["u"]."'")->first();
          @endphp
        </div>
        <div class="col-xs-12 col-md-4">
          <div class="camera-roll">
            <video autoplay id="player" muted class="camera" playsinline></video>
            <img src="{{asset('/images/logo.png')}}" class="logo">
          </div>
          <br>
          <div class="row">
            <div class="col">
              <div class="card">
                <div class="card-body">
                  <h6 class="card-subtitle mb-2 text-muted">{{$cartera->concepto}}</h6>
                  @include('componentes.texto_firma')
                </div>
              </div>
              <br>
              <center>
                <div class="btn btn-danger grabador">
                  <i class="fas fa-video"></i> Comenzar a grabar.
                </div>

                <div class="btn btn-danger sign d-none">
                  <i class="fas fa-cloud-upload-alt"></i> Subir firma.
                </div>
              </center>
              <div class="pbar d-none">
                <br>
                <div class="progress">
                  <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <br>
              <br>
            </div>
          </div>
        </div>
        <div class="col">

        </div>
      </div>
      </div>
    </div>


    <script>
    var video = document.getElementById("player");
    var play = false;
    $(function(){
      $(".grabador").bind("click",stopPlay);
      $(".sign").bind("click",upload);
      $("#staticBackdrop").modal();
    });
    function stopPlay(){
      if(play == false){
        startRecording();
        $(".grabador").html('<i class="fas fa-pause"></i> Parar grabación.');
        $(".sign").addClass("d-none")
        play = true;
      } else {
        stopRecording();
        $(".grabador").html('<i class="fas fa-redo"></i> Repetir grabación.');
        $(".sign").removeClass("d-none");
        play = false;
      }
    }
    const constraints = {
      audio: true,
      video: true
    };
    navigator.mediaDevices.getUserMedia(constraints).
    then((stream) => {
      handleSuccess(stream); // This is basic handler with stream input.
    });

    function handleSuccess(stream) {
      console.log('getUserMedia() got stream: ', stream);
      window.stream = stream;
      success();
    }

    function success() {
      video.srcObject = window.stream;
    }

    function startRecording() {
			recordedBlobs = [];
			let options = { mimeType: 'audio/mpeg' };
			if (!window.MediaRecorder.isTypeSupported(options.mimeType)) {
				console.log(options.mimeType + ' is not Supported');
				options = { mimeType: '' };
			}
			try {
				mediaRecorder = new window.MediaRecorder(window.stream, options);
			} catch (e) {
				console.error('Exception while creating MediaRecorder: ' + e);
				alert('Exception while creating MediaRecorder: '
					+ e + '. mimeType: ' + options.mimeType);
				return;
			}
			console.log('Created MediaRecorder', mediaRecorder, 'with options', options);
			mediaRecorder.onstop = handleStop;
			mediaRecorder.ondataavailable = handleDataAvailable;
			mediaRecorder.start(10); // collect 10ms of data
			console.log('MediaRecorder started', mediaRecorder);
		}

		function stopRecording() {
			mediaRecorder.stop();
			console.log('Recorded Blobs: ', recordedBlobs);
		}
    function handleError(error) {
			console.log('navigator.getUserMedia error: ', error);
		}

		function handleDataAvailable(event) {
			if (event.data && event.data.size > 0) {
				recordedBlobs.push(event.data);
			}
		}

		function handleStop(event) {
			console.log('Recorder stopped: ', event);
		}

    function upload2(){
      let blob = new Blob(recordedBlobs, { type: 'audio/mpeg' });
      fetch("/alumnos/signvideo", {method: 'post', body: blob})
    }

    function upload() {
      $(".grabador").addClass("d-none");
      $(".pbar").removeClass("d-none");
      $(this).text("Subiendo ...");
      let blob = new Blob(recordedBlobs, { type: 'audio/mpeg' });
      var reader = new FileReader();
      reader.onload = function(event){
        var fd = {};
        fd["fname"] = new Date().getTime();
        fd["data"] = event.target.result;
        $.ajax({
          type: 'POST',
          url: '/alumnos/signvideo?cid={{$_REQUEST["u"]}}',
          data: fd,
          dataType: 'text',
          xhr: function() {
              var xhr = new window.XMLHttpRequest();
              xhr.upload.addEventListener("progress", function(evt) {
                  if (evt.lengthComputable) {
                      var percentComplete = (evt.loaded / evt.total) * 100;
                      $(".progress-bar").attr("aria-valuenow",Math.round(percentComplete));
                      $(".progress-bar").text(Math.round(percentComplete)+"%");
                      $(".progress-bar").attr("style","width:"+Math.round(percentComplete)+"%;");
                  }
             }, false);
             return xhr;
          }
        }).done(function(data) {
            $(".sign").addClass("d-none");
            $(".pbar").addClass("d-none");
            location.href= "/process";
        });
      };
      reader.readAsDataURL(blob);
    }

    function download() {
			let blob = new Blob(recordedBlobs, { type: 'audio/mpeg' });
			let url = window.URL.createObjectURL(blob);
			let a = document.createElement('a');
			a.style.display = 'none';
			a.href = url;
			a.download = 'SOURCE.mp4';
			document.body.appendChild(a);
			a.click();
			setTimeout(function () {
				document.body.removeChild(a);
				window.URL.revokeObjectURL(url);
			}, 100);
		}
    </script>
  </body>
</html>
