<div class="card">
  <div class="card-body">
    <h6 class="card-subtitle mb-2 text-muted">Documentos de inscripción</h6>
    <hr>
    <div class="row">
      <div class="col">
        @if (count($c->documentos) > 0)
          @foreach ($c->documentos as $documento)
            <div class="" style="height:40px;">
              <div class="clearfix">
                <div class="float-start">
                  <i class="fa {{$documento->fasm()}}"></i>
                  {{str_replace("."," ",$documento->titulo)}}
                </div>
                <div class="float-end">
                  <a href="/documentos/download/{{md5($documento->id)}}" class="btn btn-sm btn-info">
                    <i class="fa fa-download"></i>
                  </a>
                  <a target="_blank" href="/documentos/watchar/{{md5($documento->id)}}" class="btn btn-sm btn-success">
                    <i class="fa fa-eye"></i>
                  </a>
                </div>
              </div>
            </div>
          @endforeach
        @else
          <div>
            <span class="texto">No hay documentos</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
