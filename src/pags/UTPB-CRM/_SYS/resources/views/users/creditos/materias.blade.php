@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-start">
              <h5 class="card-title">Cartera</h5>
              <h6 class="card-subtitle mb-2 text-muted">Pago de recuperación por materias</h6>
            </div>
          </div>
          <hr>
          <div class="card-body">
            <div class="status text-center">
              <h5 class="status_dat">Conectando con rectoria ...</h5>
              <i class="fas fa-cog fa-spin fa-3x"></i>
            </div>
            <table class="table table-striped">
              <thead class="bg-dark">
                <th>Nombre</th>
                <th>Costo</th>
                <th>Materia</th>
                <th>Materia</th>
                <th>Materia</th>
                <th>Materia</th>
                <th>Recuperación</th>
              </thead>
              <tbody class="materias">

              </tbody>
              <tfoot>
                <tr>
                  <td>Total de materias:</td>
                  <td class="total_materias">0</td>
                  <td>Costo por materias:</td>
                  <td class="costo_materias">350</td>
                  <td>Retorno:</td>
                  <td class="recuperacion_materias">0</td>
                </tr>
                <tr>
                  <td>Retorno - Costos:</td>
                  <td class="retorno_materias">0</td>
                  <td>Rectoria(70%):</td>
                  <td class="rectoria">0</td>
                  <td>Utilidad(30%):</td>
                  <td class="utilidad">0</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('styles')
  <style media="screen">
    hr{
      height:10px;
      background-color:#ccc;
      border:0;
    }
    .text-muted{
      color:#BD773E !important;
    }
  </style>
@endsection
@section('scripts')
  <script>
    var claves = [];
    var mec = 0;
    var total = 0;
    $(function(){
      $.get("https://plataformaunisant.mx/unisant/apiEstudy/externos/alumno/consultaOrizaba.php?token=4ba07dd78a8a6bc15844adebebffc342"
        ,function(data){
          $(".status_dat").text("Cargando materias ...");
          if(data.status == "1"){
            $.each(data.response,function(i,alumno){
              if(alumno.estado_alumno == "Activo" && alumno.pagos == "Pago por materias Licenciaturas" && claves.indexOf(alumno.clave_alumno) == -1){
                claves.push(alumno.clave_alumno);
                var tr = $("<tr>");
                var nombre = $("<a>").attr("href","/clientes/bymat/get?m="+alumno.clave_alumno).text(alumno.nombre+" "+alumno.primer_apellido);
                td = $("<td>");
                td.append(nombre);
                tr.append(td);
                var td = $("<td>");
                var pago = 1520;
                $.get("/clientes/preciomateria/get?m="+alumno.clave_alumno,function(data){
                  if(data != ""){
                    pago = data;
                    console.log(data);
                  }
                  td.text(f.format(pago));
                  tr.append(td);

                  var lm = 0;
                  $.each(alumno.materias_en_curso,function(ind,materia){
                      mec++;
                      td = $("<td>");
                      td.text(materia.asignatura);
                      tr.append(td);
                      lm++;
                  });
                  for (var i = 0; i <= 4-lm; i++) {
                    td = $("<td>");
                    td.text("Sin materia");
                    tr.append(td);
                  }
                  var tem = lm*pago;
                  total = total + tem;
                  td.text(f.format(tem));
                  tr.append(td);
                  $(".materias").append(tr);

                });
              }
            });

            $(".status").empty();
            setTimeout(function(){
              $(".total_materias").text(mec);
              $(".recuperacion_materias").text(f.format(total));
              $(".costo_materias").text(f.format(350*mec));
              $(".retorno_materias").text(f.format(total-(350*mec)));
              $(".rectoria").text(f.format((total-(350*mec))*.7));
              $(".utilidad").text(f.format((total-(350*mec))*.3));
            },2000);
          } else {
            $(".status_dat").text("No se pudo conectar con rectoria").addClass("text-danger");
          }
        });
    });

    var f = new Intl.NumberFormat('es-MX', {
      style: 'currency',
      currency: 'MXN',
    });
  </script>
@endsection
