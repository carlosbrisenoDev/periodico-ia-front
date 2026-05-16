<div class="card">
  <div class="card-body">
    <h5 class="card-title">Notas</h5>
    <h6 class="card-subtitle mb-2 text-muted">&Uacute;ltima: </h6>
    <hr>
    <div class="row">
      <div class="col">
        @php
          $n = \App\notas_cliente::where('cliente_id',$c->id)->orderBy("id","desc")->get();
          $beca = false;
        @endphp
        @if (count($n) > 0)
          @foreach ($n as $no)
            @if ($no && $no->usuario)
            <div class="card">
              <div class="card-body">
                  <small>
                    <div class="row">
                      <div class="col">
                        {{$no->usuario->name}}
                      </div>
                      <div class="col text-right">
                        <a class="float-end" target="_blank"  href="/bandeja/nuevo/enviar?a={{$no->usuario->email}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                          <i class="far fa-envelope"></i>
                        </a>
                      </div>
                    </div>
                  </small>
                {{$no->nota}}
                @php
                  $beca = strstr($no->nota,"beca") ? true : false;
                @endphp
                <p align="right">
                  <small>
                    {{\Carbon\Carbon::parse($no->created_at)->diffForHumans()}}
                  </small>
                </p>
                <p>
                  @if (count($no->likes) > 0)
                    @php
                      $nombres = "";
                      $is = false;
                      foreach ($no->likes as $like)
                        {
                          $nombres .= ($like && $like->usuario ? $like->usuario->name : "Sin nombre").", ";
                          if($like->usuario_id == Auth::user()->id){
                            $is = true;
                          }
                        }
                      $islike = ($is==true) ? "unlike" : "like";
                    @endphp
                        <a href="/ventas/{{$islike}}/dar?cid={{md5($no->id)}}" class="btn btn-link btn-sm"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="{{"$nombres ha reaccionado a este comentario"}}"
                          >
                          {{count($no->likes)}} <i class="far fa-hand-spock"></i>
                        </a>
                      @else
                        <a href="/ventas/like/dar?cid={{md5($no->id)}}" class="btn btn-link btn-sm"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="Reacciona"
                          >
                          0 <i class="far fa-hand-spock"></i>
                        </a>
                  @endif
                </br>
                </p>
              </div>
            </div>
            <hr>
            @endif
          @endforeach
        @endif
      </div>
    </div>
    <form class="" action="/ventas/nota" method="post">
    <input type="hidden" name="cliente_id" value="{{$c->id}}">
    <div class="row">
        <div class="col-12">
          <textarea name="comentario" class="form-control" placeholder="Agregar nota ..."></textarea>
          <hr>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar comentario">
            <i class="fas fa-comment-alt"></i> Escribir comentario
          </button>
        </div>
    </div>
  </form>
  </div>
</div>
