<div class="col-md-12 clearfix">
  <nav aria-label="Page navigation" class="pull-{{$pull}}">
    <ul class="pagination">
      <li>
        <a href="{{$nav->previousPageUrl()}}#wrap" aria-label="Anterior">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      @for($i=1; $i <= (($nav->total() % 20 > 0) ? $nav->total()/20 + 1 : $nav->total()/20); $i++)
        @if($nav->currentPage() == $i)
          <li class="active"><a href="{{$nav->url($nav->currentPage())}}#wrap">{{$nav->currentPage()}}</a></li>
        @else
          <li><a href="{{$nav->url($i)}}#wrap">{{$i}}</a></li>
        @endif
      @endfor
      <li>
        <a href="{{$nav->nextPageUrl()}}#wrap" aria-label="Siguiente">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
  </nav>
</div>
