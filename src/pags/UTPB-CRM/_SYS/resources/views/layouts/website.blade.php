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
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{asset('/assets/img/apple-icon.png')}}">
  <link rel="icon" type="image/png" href="{{asset('/assets/img/favicon.png')}}">
  <link rel="stylesheet" href="{{asset("/css/app.css")}}">
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="stylesheet" href="{{asset("/css/modify.css")}}">
  <link rel="stylesheet" href="{{asset("/css/website.css")}}">
  <link rel="stylesheet" href="{{asset("/css/alert.css")}}">
  @yield("styles")
  @stack('styles')
  <title>
    eCustomerManager
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{asset('/assets/css/nucleo-icons.css')}}" rel="stylesheet" />
  <link href="{{asset('/assets/css/nucleo-svg.css')}}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{asset('/assets/css/nucleo-svg.css')}}" rel="stylesheet" />
  <!-- CSS Files -->
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
  </style>
</head>

<body class="sign-in-illustration">

  <!-- Navbar -->
  <section>
    <div class="page-header min-vh-100">
      <div class="container">
        <div class="row">
          <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
            <div class="card card-plain">
              <div class="card-header pb-0 text-left">
                <h4 class="font-weight-bolder">Iniciar sesión</h4>
                <p class="mb-0">Introduce tu correo electrónico e ingresa</p>
              </div>
              <div class="card-body">
                <form role="form" method="post" action="{{route("login")}}">
                  @csrf
                  <div class="mb-3">
                    <input type="email" class="form-control form-control-lg" name="email" placeholder="webmaster@e-dav.net" aria-label="Email" aria-describedby="email-addon">
                  </div>
                  <div class="mb-3">
                    <input type="password" class="form-control form-control-lg" name="password" placeholder="********" aria-label="Password" aria-describedby="password-addon">
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Recuerdame</label>
                  </div>
                  <div class="text-center">
                    <button type="submit" class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0">Ingresar</button>
                  </div>
                </form>
              </div>
              <div class="card-footer text-center pt-0 px-lg-2 px-1">
                <p class="mb-4 text-sm mx-auto">
                  ¿No tienes cuenta?
                  <a href="mailto:webmater@e-dav.net" class="text-primary text-gradient font-weight-bold">
                    Webmaster
                  </a>
                </p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-12 col-lg-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
            <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center">
              <img src="{{asset('/assets/img/shapes/pattern-lines.svg')}}" alt="pattern-lines" class="position-absolute opacity-4 start-0">
              <div class="position-relative">
                <img class="max-width-500 w-100 position-relative z-index-2" src="{{asset('/assets/img/illustrations/BLANCO_LOGO.png')}}">
              </div>
              <h4 class="mt-5 text-white font-weight-bolder">"Electronic Customer Manager"</h4>
              <p class="text-white">
                Sistema gestor de clientes en linea
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--   Core JS Files   -->
  <script src="{{asset('/assets/js/core/popper.min.js')}}" type="text/javascript"></script>
  <script src="{{asset('/assets/js/core/bootstrap.min.js')}}" type="text/javascript"></script>
  <script src="{{asset('/assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
  <!--  Plugin for Parallax, full documentation here: https://github.com/wagerfield/parallax  -->
  <script src="{{asset('/assets/js/plugins/parallax.min.js')}}"></script>
  <!-- Control Center for Soft UI Kit: parallax effects, scripts for the example pages etc -->
  <!--  Google Maps Plugin    -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTTfWur0PDbZWPr7Pmq8K3jiDp0_xUziI"></script>
  <script src="{{asset('/assets/js/soft-design-system.min.js?v=1.0.9')}}" type="text/javascript"></script>
  <!-- JavaScript Bundle with Popper -->
  <script src="https://code.jquery.com/jquery-3.6.1.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  @yield('scripts')
  @stack('scripts')
</body>

</html>
