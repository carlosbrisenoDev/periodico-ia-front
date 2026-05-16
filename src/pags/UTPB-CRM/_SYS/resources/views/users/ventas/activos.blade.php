@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-start">
              <h5 class="card-title">Alumnos activos</h5>
              <h6 class="card-subtitle mb-2 text-muted">Lista de alumnos inscritos</h6>
            </div>
            <div class="float-end">
              {{-- <a href="/alumnos/inscritoscsv/get" class="btn btn-success">
                <i class="fa fa-download"></i> Descargar
              </a> --}}
            </div>
          </div>
          <hr>

                @php
                  $i = 1;
                  $cls = \App\cliente::where("matricula","<>","")->where("nombre","not like","%PRUEBA%")->where("status","4")->where("baja",NULL);
                @endphp
                <div class="list-group">
                @foreach ($cls->get() as $cr)
                  @php
                    $c = $cr->isinscripcion;
                    $cl = $cr;
                  @endphp
                  @if (!strstr($cr->nombre,"PRUEBA"))
                      <div class="list-group-item">
                        <div class="row">
                          <div class="col"><span class="matricula none">{{$cr->matricula}}</span> @include('componentes.iconos')</div>
                          <div class="col">
                            <a href="/ventas/cliente?cid={{md5($cr->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para ver">
                              {{($c != NULL) ? $c->nombre_completo : $cr->nombre}}
                            </a>
                          </div>
                          <div class="col-8">
                            <div class="row status">
                              <div class="_M{{$cl->matricula}}_1 col-3">
                                <div role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-value="100" style="width:100%;" class="text-light progress bg-danger">
                                  NO TOMADA
                                </div>
                              </div>
                              <div class="_M{{$cl->matricula}}_2 col-3">
                                <div role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-value="100" style="width:100%;" class="text-light progress bg-danger">
                                  NO TOMADA
                                </div></div>
                              <div class="_M{{$cl->matricula}}_3 col-3">
                                <div role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-value="100" style="width:100%;" class="text-light progress bg-danger">
                                  NO TOMADA
                                </div></div>
                              <div class="_M{{$cl->matricula}}_4 col-3">
                                <div role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-value="100" style="width:100%;" class="text-light progress bg-danger">
                                  NO TOMADA
                                </div></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    @else
                  @endif
                @endforeach
              </div>

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
  <script type="text/javascript">
    $(function(){
      recovery();
    });
    function recovery(){
      if($(".matricula")[0] != undefined){
        var url = "https://plataformaunisant.mx/unisant/apiEstudy/externos/alumno/consulta.php";
        var token = "4ba07dd78a8a6bc15844adebebffc342";
        var matricula = $(".matricula").first().text();
        $.get(url+"?matricula="+matricula+"&token="+token,function(data){
          if(data.status == 1){
            $.each(data.response.materias_en_curso,function(i,el){
              var progress = $("<div>").addClass("progress");
              var bg = el.porcentaje_avance >= 70 ? "success" : (el.porcentaje_avance == 0 ? "danger" : "warning");
              progress.append($("<div>").addClass("text-center text-dark progress-bar progress-bar-striped bg-"+bg)
              .attr("role","progressbar")
              .attr("style","width:"+el.porcentaje_avance+"%")
              .attr("aria-valuenow",el.porcentaje_avance)
              .attr("aria-valuemin",0)
              .attr("aria-valuemax",100)
              .text(el.porcentaje_avance+"%")
              );
              $("._M"+matricula+"_"+(i+1)).empty();
              $("._M"+matricula+"_"+(i+1)).append(progress);
            });
          }
          $(".matricula").first().removeClass("matricula");
          recovery();
        });
      }
    }
  </script>
@endsection
