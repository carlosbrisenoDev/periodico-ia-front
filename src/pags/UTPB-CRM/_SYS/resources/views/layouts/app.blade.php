<!--
=========================================================
* Soft UI Design System - v1.0.9
=========================================================

* Product Page:  https://www.creative-tim.com/product/soft-ui-design-system 
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Coded by www.creative-tim.com

 =========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. -->
<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">
<head>
  <meta charset="utf-8" />
  <meta name="csrf-token" content="{{csrf_token()}}">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{asset('/assets/img/apple-icon.png')}}">
  <link rel="icon" type="image/png" href="{{asset('/assets/img/favicon.png')}}">
  <link rel="stylesheet" href="{{asset("/css/app.css")}}">
  <link rel="stylesheet" href="{{asset("/css/modify.css")}}">
  <link rel="stylesheet" href="{{asset("/css/website.css")}}">
  <link rel="stylesheet" href="{{asset("/css/alert.css")}}">
  <link rel="stylesheet" href="{{asset("/css/newapp.css")}}">
  <link rel="stylesheet" href="{{asset("/css/context.css")}}">
  <link rel="stylesheet" href="{{asset("/css/bootstrap-tagsinput.css")}}">
  <link rel="manifest" href="{{asset("/js/manifest.json")}}">
  <title>
    eCustomerManager
  </title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link href="{{asset('/assets/css/nucleo-icons.css')}}" rel="stylesheet" />
  <link href="{{asset('/assets/css/nucleo-svg.css')}}" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/3421d9875f.js" crossorigin="anonymous"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script type="text/javascript">
      (function(c,l,a,r,i,t,y){
          c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
          t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
          y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
      })(window, document, "clarity", "script", "fnlm4nnpeb");
  </script>
  <link href="{{asset('/assets/css/nucleo-svg.css')}}" rel="stylesheet" />
  <link id="pagestyle" href="{{asset('/assets/css/soft-design-system.css?v=1.0.9')}}" rel="stylesheet" />
  <style>
    :root {
      --bs-primary: #0ea5a6;
    }
    .bg-gradient-primary {
      background-image: linear-gradient(310deg, #22c55e 0%, #0ea5e9 100%) !important;
    }
    .btn-primary,
    .btn.bg-gradient-primary {
      background-color: #0ea5a6 !important;
      border-color: #0ea5a6 !important;
      color: #fff !important;
    }
    .btn-primary:hover,
    .btn.bg-gradient-primary:hover {
      background-color: #0284c7 !important;
      border-color: #0369a1 !important;
    }
    .text-primary {
      color: #0ea5a6 !important;
    }
    .text-primary.text-gradient {
      background-image: linear-gradient(310deg, #22c55e 0%, #0ea5e9 100%) !important;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .badge-primary,
    .bg-primary {
      background-color: #0ea5a6 !important;
    }
    .bg-gradient-dark {
      background-image: linear-gradient(310deg, #166534 0%, #1d4ed8 100%) !important;
    }
    #myModalCC {
      overflow-y: hidden;
    }
    .modal-header {
      cursor: move;
    }
  </style>
  @yield("styles")
  @stack('styles')
  {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script> --}}
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
  {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script> --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
  {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous"/> --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css">
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.2/moment.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="//connect.facebook.net/en_US/all.js#xfbml=1&appId=486984498523003" id="facebook-jssdk"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-app.js"></script>
  <script defer src="https://code.getmdl.io/1.1.3/material.min.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-analytics.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
  
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register("/service-worker.js");
      });
    }
  </script>
</head>

<body class="blog-author bg-gray-100">
  
  @include('componentes.menu-header')

  <section class="px-5 mb-4 position-relative">
    <div class="row">
      @if(\Auth::user()->hide === 0)
        <div class="col-md-3 col-sm-12">
          @include("componentes.roles.default")
        </div>
      @else
        {{-- <a href="/usuarios/close/do">
          <i class="fa fa-bars" aria-hidden="true"></i>
        </a> --}}
      @endif
      <div class="col">
        @yield("content")
      </div>

      <script>
        console.log()
      </script>
      
  </section>
  {{-- <div class="modal fade" data-backdrop="false" id="myModalCC" tabindex="-1" role="dialog" aria-labelledby="myModalCCLabel" style="display: block;padding-left: 16px;width: 616px;height: 950px">
    <div class="modal-dialog" role="document">
      <div class="modal-content" style="height:850px;">
        <div class="modal-header ui-draggable-handle">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="    background: transparent;border: none;font-size: 27px;"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="myModalCCLabel">Call Center</h4>
        </div>
        <div class="modal-body" style="height: 100%;">
           <iframe width="100%" height="750px" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" ></iframe>
        </div>
      </div>
    </div>
  </div> --}}
  <div aria-live="polite" aria-atomic="true" style="position: relative; min-height: 200px;">
    <div style="position: fixed; bottom: 15px; right: 15px;" class="pops">
      @if (session('status'))
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000">
          <div class="toast-header">
              <div class="float-start">
                <small class="text-muted">Justo ahora</small>
              </div>
          </div>
          <div class="toast-body">
            {{session('status')}}
          </div>
        </div>
      @endif
    </div>
  </div>
  @if (session('error'))
      <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000">
        <div class="toast-header">
          <i class="fa fa-info-circle"></i>
          <strong class="mr-auto" style="padding-left:5px;"> SII</strong>
          <small class="text-muted">Justo ahora</small>
          <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="toast-body">
          {{ session('error') }}
        </div>
      </div>
  @endif

  <footer class="footer py-5 bg-gradient-dark position-relative overflow-hidden">
    <img src="{{asset('/assets/img/shapes/waves-white.svg')}}" alt="pattern-lines" class="position-absolute start-0 top-0 w-100 opacity-6">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-lg-8">
          <p class="mb-0 text-lg text-white font-weight-bold">
            Powered by CETIA Media
          </p>
        </div>
      </div>
    </div>
  </footer>
  @yield('pop')
    @yield('modal')
  <!-- -------- END FOOTER 5 w/ DARK BACKGROUND ------- -->
  <!--   Core JS Files   -->
  <script src="{{asset('/assets/js/core/popper.min.js')}}" type="text/javascript"></script>
  <script src="{{asset('/assets/js/core/bootstrap.min.js')}}" type="text/javascript"></script>
  <script src="{{asset('/assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
  <script src="{{asset('/assets/js/plugins/parallax.min.js')}}"></script>
  <script src="{{asset('/assets/js/soft-design-system.min.js?v=1.0.9')}}" type="text/javascript"></script>
  @if(auth()->user())
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      
      var ecmUsr = {usrEcmIdentifier: "{{auth()->user()->id}}",unique_token:'xEyrx:FW[$D@eE}pnA(CECqi6JF=}XgH6vbFdJk9nY(T4;27B('};
      
      localStorage.setItem("ecmUsr", JSON.stringify(ecmUsr));
    });
  </script>
  @endif
  <!-- JavaScript Bundle with Popper -->
  <!-- Scripts -->
  <script src="{{ asset('js/ion.sound.js') }}"></script>
  <script src="{{ asset('js/popper.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-messaging.js"></script>
  <script src="{{ asset('js/lang.js') }}"></script>
  <script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>
  <script src="{{ asset('js/context.js?r=1') }}"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
  
  <script>
    $("#myModalCC").draggable({
      handle: ".modal-header"
    });

    $("#myModalCC").on('shown.bs.modal', function (e) {
      $(".modal iframe").attr('src', "https://edav.my.connect.aws/login" ); 
    })

    $("#myModalCC").on('hide.bs.modal', function (e) {
      $(".modal iframe").attr('src'," "); 
    })

  </script>
  <!--<script src="{{ asset('js/messaging.js') }}"></script>-->
  <script type="text/javascript">
    $(document).ready(function() {
      $(window).keydown(function(event){
        if(event.keyCode == 13 && ($(event.target)[0]!=$("textarea")[0])) {
          event.preventDefault();
          return false;
        }
      });
    });
    $(document.body).ready(function(){
      var h = $(document).height();
      $(".cont").css({"min-height":h});
      console.log(h);
      $('.toast').toast('show',{delay:50000});
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
      });
      var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
      var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
      })
    });
    $('.count').each(function () {
      var $this = $(this);
      jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
        duration: 1000,
        easing: 'swing',
        step: function () {
          $this.text(Math.ceil(this.Counter));
        }
      });
    });
  </script>
  <script>
    
    let installButton = document.querySelector('.add-button-pwa');
    let promptEvent;

    
    window.addEventListener("beforeinstallprompt", (e) => {

      // Almacenamos el evento para usarlo más tarde
      promptEvent = event;
      
      // Mostramos el botón de instalación
      $('.menu-button-pwa').css('display','block');
      
      
    });

    installButton.addEventListener('click', () => {
      // Ocultamos el botón de instalación
      $('.menu-button-pwa').css('display','none');
      
      // Mostramos la ventana de confirmación al usuario
      promptEvent.prompt();
      // Esperamos la respuesta del usuario
      promptEvent.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
          console.log('El usuario aceptó la instalación de la PWA');
        } else {
          console.log('El usuario canceló la instalación de la PWA');
        }
        // Limpiamos el evento para que no pueda ser utilizado de nuevo
        promptEvent = null;
      });
    });
  </script>
  @yield('scripts')
  @stack('scripts')
</body>

</html>
