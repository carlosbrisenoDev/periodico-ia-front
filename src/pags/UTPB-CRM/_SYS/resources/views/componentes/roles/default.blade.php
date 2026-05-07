<div class="close">
  <a href="/usuarios/close/do">
    <i class="fas fa-times-circle"></i>
  </a>
</div>
<img src="{{asset("/assets/img/illustrations/NEGRO_LOGO.png")}}" class="img-fluid" alt="">
<div class="row">
  <div class="col">
    <div class="row">
      <div class="col">
        <div class="list-group rounded-0">
         <a href="/" class="list-group-item list-group-item-action" aria-current="true">
           Inicio
         </a>
        </div>
      </div>
    </div>
    @foreach (\Auth::user()->role->sortBy("id") as $rl)
      <div class="row">
        <div class="col">
          <div class="list-group rounded-0">
           <a href="/r/{{strtolower($rl->role->role)}}" class="list-group-item list-group-item-action" aria-current="true">
             {{$rl->role->role}}
             @php
               $dir =  __DIR__."/../../../resources/views/componentes/roles/".strtolower($rl->role->role).".blade.php";
             @endphp
             @if (file_exists($dir))
                 @include("componentes.roles.".strtolower($rl->role->role))
               @else
                 <i class="text-danger fas fa-exclamation-circle"></i>
             @endif
           </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
