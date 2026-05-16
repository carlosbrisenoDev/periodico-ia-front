
<div class="row">
  <div class="col-md-4 col-sm-12">
    <a href="/">
      <img src="{{ asset('images/'.((set_active('shirushi')) ? "logo2.png" : "logo.png"))}}" alt="Shirushi" class="logo">
    </a>
  </div>
  <div class="col-md-8 col-sm-12">
    @if(Auth::guest())
    <table height="100%" align="right">
      <tr>
        <td>
          <a class="white login" data-toggle="modal" data-target="#exampleModalCenter" href="#" >Iniciar Sesión</a>
        </td>
        <td>
          <a class="white" href="/shirushi/pideadomicilio">Registrate</a>
        </td>
      </tr>
    </table>
  @elseif (Auth::user())
      @php
        $cart = count(Session::get("cart"));
      @endphp
      <table height="100%" align="right">
        <tr>
          <td class="align-middle">
            <a href="/" class="btn btn-default">
              @php
                $a=explode(" ",Auth::user()->name);
              @endphp
                HOLA, <span style="text-transform:uppercase;">
                  {{$a[0]}}
                </span>
            </a>
          </td>
          <td>
            <a href="/shirushi/cart" class="btn btn-default">
              <i class="fas fa-shopping-cart"></i> <span class="badge badge-primary">{{$cart}}</span>
            </a>
          </td>
          <td>
            <a href="/" class="btn btn-default">
                <i class="fas fa-coins"></i> MONEDERO <span class="badge badge-primary">${{number_format(Auth::user()->cash,2)}}</span>
            </a>
          </td>
          <td>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                SALIR <i class="fas fa-sign-out-alt"></i>
            </a>
          </td>
        </tr>
      </table>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          {{ csrf_field() }}
      </form>
    @endif
  </div>
</div>


<nav class="navbar navbar-expand-lg navbar-light">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link {{set_active("shirushi/menu")}}" href="/">Inicio </a>
      </li>
    </ul>
  </div>
</nav>
