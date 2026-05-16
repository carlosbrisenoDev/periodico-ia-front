<div class="row">
  @php
    $columns = Schema::getColumnListing('inscripciones');
  @endphp
  <h6 class="card-subtitle mb-2 text-muted">Inscripción</h6>
  <hr>
  @foreach ($columns as $key => $val)
    @php
      $_val = NULL;
      eval("\$_val = \$c->isinscripcion->$val;");
      $_name = ucfirst($val);
      $_name = str_replace("_"," ",$_name);
    @endphp
    <div class="col-12 col-md-12 col-lg-6">
      <label for="">{{$_name}}</label>
      <input type="text" class="form-control" value="{{$_val}}">
    </div>
  @endforeach
  <hr>
  <a href="/clientes/llenar/fromurl?cid={{md5($c->id)}}" class="btn btn-primary">
    Llenar desde Plataforma
  </a>
</div>
