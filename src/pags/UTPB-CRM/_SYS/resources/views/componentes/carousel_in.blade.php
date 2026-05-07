
<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    @for($i=1; $i <= $cantidad; $i++)
      @if($i==1)
        <li data-target="#myCarousel" data-slide-to="{{$i}}" class="active"></li>
      @else
        <li data-target="#myCarousel" data-slide-to="{{$i}}" class=""></li>
      @endif
    @endfor
  </ol>
  <div class="carousel-inner" role="listbox">
    @for($i=1; $i <= $cantidad; $i++)
        <div class="item @if($i==1)active @endif">
          @if(isset($link))<a href="/casos/{{$i}}/casos de exito#wrap">@endif
          <img src="{{$ruta}}{{$i}}.{{$formato}}" class="img-responsive" alt="">
          @if(isset($link))</a>@endif
        </div>
    @endfor
  </div>
  <a class="carousel-control" href="#myCarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Siguiente</span>
  </a>
  <a class="nright carousel-control" href="#myCarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Anterior</span>
  </a>
</div><!-- /.carousel -->
