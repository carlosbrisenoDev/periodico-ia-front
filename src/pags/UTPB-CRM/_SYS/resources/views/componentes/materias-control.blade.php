<h6 class="card-subtitle mb-2 text-muted">Materias en curso</h6>
<hr>
<table class="table table-striped">
  <thead class="table-dark">
    <th>Asignatura</th>
    <th>Clave</th>
    <th>Progreso</th>
    <th>%</th>
  </thead>
  <tbody class="materias_en_curso">

  </tbody>
</table>
<h6 class="card-subtitle mb-2 text-muted">Materias cursadas</h6>
<hr>
<table class="table table-striped">
  <thead class="table-dark">
    <th>Asignatura</th>
    <th>Clave</th>
    <th>Cal.</th>
  </thead>
  <tbody class="materias_cursadas">

  </tbody>
</table>
<h6 class="card-subtitle mb-2 text-muted">Materias por cursar</h6>
<hr>
<table class="table table-striped">
  <thead class="table-dark">
    <th>Asignatura</th>
    <th>Clave</th>
    <th>Cal.</th>
  </thead>
  <tbody class="materias_por_cursar">

  </tbody>
</table>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  $(function(){
    google.charts.load('current', {'packages':['corechart']});
    var url = "https://plataformaunisant.mx/unisant/apiEstudy/externos/alumno/consulta.php";
    var token = "4ba07dd78a8a6bc15844adebebffc342";
    var matricula = "{{$c->matricula}}";
    console.log(url+"?matricula="+matricula+"&token="+token);
    $.get(url+"?matricula="+matricula+"&token="+token,function(data){
      console.log(data)
      if(data.status == 1){
        $.each(data.response.materias_cursadas,function(i,el){
          var color_class = parseInt(el.calificacion) >= 7 ? "text-success" : "text-danger";
          var tr = $("<tr>");
          tr.append($("<td>").text(el.asignatura));
          tr.append($("<td>").text(el.asignatura_clave));
          tr.append($("<td>").text(el.calificacion).addClass(color_class));
          $(".materias_cursadas").append(tr);
        });
        $.each(data.response.materias_por_cursar,function(i,el){
          var tr = $("<tr>");
          tr.append($("<td>").text(el.asignatura));
          tr.append($("<td>").text(el.asignatura_clave));
          tr.append($("<td>").text("?"));
          $(".materias_por_cursar").append(tr);
        });
        $.each(data.response.materias_en_curso,function(i,el){
          var tr = $("<tr>");
          var progress = $("<div>").addClass("progress");
          var bg = el.porcentaje_avance >= 70 ? "success" : (el.porcentaje_avance == 0 ? "danger" : "warning");
          progress.append($("<div>").addClass("text-center text-dark progress-bar progress-bar-striped bg-"+bg)
          .attr("role","progressbar")
          .attr("style","width:"+el.porcentaje_avance+"%")
          .attr("aria-valuenow",el.porcentaje_avance)
          .attr("aria-valuemin",0)
          .attr("aria-valuemax",100)
          );
          tr.append($("<td>").text(el.asignatura));
          tr.append($("<td>").text(el.asignatura_clave));
          tr.append($("<td>").append(progress));
          tr.append($("<td>").append(el.porcentaje_avance+"%"));
          $(".materias_en_curso").append(tr);
        });
        setTimeout(function(){
          google.charts.setOnLoadCallback(drawChart(
            data.response.materias_cursadas.length,
            data.response.materias_por_cursar.length,
            data.response.materias_en_curso.length
          ))
        },1000);
      }
    });
  })


  function drawChart(x,y,z) {

    var data = google.visualization.arrayToDataTable([
      ['estado', 'Materias'],
      ['Cursadas',     x],
      ['Por cursar',      y],
      ['En curso',  z]
    ]);

    var options = {
      title: 'Materias'
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart_materias'));

    chart.draw(data, options);
  }
</script>
