{{-- <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
        <div class="col-12">
            <nav class="navbar navbar-expand-lg  blur blur-rounded top-0 border-bottom z-index-3 shadow w-100 mt-4 d-none d-lg-block my-3 py-2">
                <div class="container-fluid">
                <a class="navbar-brand font-weight-bolder ms-3" href="" rel="tooltip" title="Designed and Coded by Creative Tim" data-placement="bottom" target="_blank">
                    eCM
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navigation">
                    <ul class="navbar-nav navbar-nav-hover mx-auto">
                    @yield('menu')
                    </ul>
            
                    <ul class="navbar-nav">
                    <button class="btn btn-sm  bg-gradient-dark  btn-round mb-0 me-1">
                        <i class="fa-solid fa-headset" style="font-size:inherit;"></i>
                        Call Center
                    </button>
                    </ul>
                </div>
                </div>
            </nav>
        </div>
    </div>
</div> --}}


<nav class="navbar navbar-expand-lg  blur border-radius-xl top-0 z-index-fixed position-sticky shadow my-3 py-2 start-0 end-0 mx-4 z-index-1" >
    <div class="container-fluid px-0">
        @if(\Auth::user()->hide === 0)
        @else
            <a href="/usuarios/close/do">
            <i class="fa fa-bars" aria-hidden="true"></i>
            </a>
        @endif
        <a class="navbar-brand font-weight-bolder ms-sm-3" href="{{url('/')}}" rel="tooltip" title="Designed and Coded" data-placement="bottom">
            eCM
        </a>
        
        {{-- <a class=" navbar-brand font-weight-bolder add-button-pwa cursor-pointer">Instalar</a> --}}
        {{-- <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon mt-2">
            <span class="navbar-toggler-bar bar1"></span>
            <span class="navbar-toggler-bar bar2"></span>
            <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button> --}}
        <button class="navbar-toggler shadow-none ms-2" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon mt-2">
            <span class="navbar-toggler-bar bar1"></span>
            <span class="navbar-toggler-bar bar2"></span>
            <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button>
        <script>
        let as = "";
        </script>
        
        <div class="collapse navbar-collapse pt-3 pb-2 py-lg-0 w-100" id="navigation">
            <ul class="navbar-nav navbar-nav-hover ms-auto">
                @yield('menu')
                {{-- <li class="nav-item my-auto ms-3 ms-lg-0">
                    <button class="btn btn-sm  bg-gradient-dark  btn-round mb-0 me-1">
                        <i class="fa-solid fa-headset" style="font-size:inherit;"></i>
                        Call Center
                    </button>
                </li> --}}
            </ul>
        </div>
    </div>
</nav>
