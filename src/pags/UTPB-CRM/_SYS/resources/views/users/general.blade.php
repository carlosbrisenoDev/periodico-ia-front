{{-- <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
      <i class="fas fa-envelope"></i>  Correo
      <span class="badge bg-success text-light">
        @php
          try{$mails = \App\Http\Controllers\bandeja::getMailCount();
          } catch(Exception $e){
            $mails = 0;
          }
          $mails
        @endphp
        {{$mails}}
      </span>
      <span class="caret"></span>
    </a>

    <ul class="dropdown-menu">
      <li>
        <a href="/bandeja/correo/listar">
          Bandeja
        </a>
      </li>
      <li>
        <a href="/bandeja/nuevo/correo">
          Nuevo
        </a>
      </li>
      <li>
        <a href="/bandeja/iosandroid/info">
          iOS / Android
        </a>
      </li>
    </ul>
</li> --}}
