@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-left">
              <h5 class="card-title">Materias</h5>
              <h6 class="card-subtitle mb-2 text-muted">Subir materias</h6>
            </div>
            <div class="float-right">
              <a href="/clientes/materiasalta/do" class="btn btn-primary">Dar de alta</a>
            </div>
          </div>
          <hr>
            @if (file_exists(storage_path()."/materias.csv"))
              <table class="table table-striped">
                @php
                $fila = 1;
                $gestor = file_get_contents(storage_path()."/materias.csv");
                $arr = explode("\n", $gestor);
                foreach ($arr as &$line) {
                  echo "<tr>";
                  $line = str_getcsv($line);
                  foreach ($line as $td) {
                      echo "<td>".$td."</td>";
                  }
                  echo "</tr>";
                }
                @endphp
              </table>
            @endif
          <hr>
          <form action="/clientes/materias" method="post" enctype="multipart/form-data">
            <input type="file" accept=".csv" name="file" value="">
            <hr>
            <input type="submit" class="btn btn-primary" name="" value="Subir">
          </form>

          </div>
        </div>
    </div>
    <div class="col-12 col-md-12 col-lg-6">

    </div>
  </div>
@endsection
@section('styles')
  <style media="screen">
    hr{
      height:10px;
      background-color:#f6f6f6;
      border:0;
    }
    .text-muted{
      color:#BD773E !important;
    }
  </style>
@endsection
@section('scripts')
  <script>
    $(".buscar").bind("keyup",function(){
      $.each($("tbody td"),function(i,e){
        if($(e).html().indexOf($(".buscar").val()) != -1){
          $($(e).parent()).css({"display":"table-row"});
        } else {
          $($(e).parent()).css({"display":"none"});
        }
      });
    });
  </script>
@endsection
