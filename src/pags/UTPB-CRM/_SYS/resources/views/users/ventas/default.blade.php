@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  @include('componentes.inscritos')
  <div class="row">
    <div class="col-md-12">
      <hr>
    </div>
  </div>
  <div class="row" id="clienteslist">
    <div class="col-md-12 col-xs-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Sin agente</h5>
          <h6 class="card-subtitle mb-2 text-muted">
            Clientes por asignar
          </h6>
          <hr>
          @php
            $clientes = App\cliente::
            whereIn("agente_id",[NULL,0])
            ->whereDate("created_at",">","2023-11-1")
            ->orderBy("id","desc")
            ->get();
          @endphp
          <table id="clientes2" class="table">
            <thead>
              <tr>
                <th class="text-dark">Agente</th>

                <th class="text-dark">#</th>
                <th class="text-dark">

                </th>
                <th class="text-dark">Tag</th>
                <th class="text-dark">Creado el</th>
                <th class="text-dark">Nombre</th>
                <th class="text-dark">T&eacute;lefono</th>
                <th class="text-dark">Correo</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($clientes as $c)
                    <tr>
                      <td>
                        {{($c->agente == null) ? "Sin agente": $c->agente->name}}
                      </td>
                      <td data-order="{{$c->id}}" style="text-align:center;">
                        <a href="/ventas/cliente?cid={{md5($c->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para ver">
                          UOV-{{\Carbon\Carbon::parse($c->created_at)->format("Y")}}-{{$c->id}}
                        </a>
                      </td>
                      <td>
                        @php
                          $cl = $c;
                        @endphp
                        @include('componentes.iconos')
                      </td>
                      @if($c->tags)
                      <td><span class="text-uppercase" style="color:{{$c->tags->color}};"> {{ $c->tags->tag }} </span></td>
                      <td>
                        {{$c->created_at}}
                      </td>
                      <td>
                        <a href="/ventas/cliente?cid={{md5($c->id)}}">
                          {{$c->full_name()}}
                        </a>
                          <small>
                            @if (\Carbon\Carbon::parse($c->created_at)->format("Y-m-d") == \Carbon\Carbon::now()->format("Y-m-d"))
                              <span class="badge bg-success text-white">Hoy</span>
                            @endif
                            @if (\Carbon\Carbon::parse($c->created_at)->format("Y-m-d") == \Carbon\Carbon::now()->subDays(1)->format("Y-m-d"))
                                <span class="badge bg-danger text-white">Ayer</span>
                            @endif
                          </small>
                      </td>
                      @else
                      <td><span style="color:#fff;"> Sin tags </span></td>
                      <td>
                        {{$c->created_at}}
                      </td>
                      <td><span style="color:#fff;"> Sin nombre</span></td>
                      @endif
                      <td>{{empty($c->telefono) ? "Sin teléfono" : $c->telefono}}</td>
                      <td>
                        @if (empty($c->correo))
                            Sin correo electrónico
                          @else
                            <a href="/bandeja/nuevo/enviar?a={{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                              {{mb_strtolower($c->correo)}}
                            </a>
                        @endif
                      </td>
                      
                    </tr>
                  @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-12 col-xs-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Últimos 50 registrados</h5>
          <h6 class="card-subtitle mb-2">
            <a {{!Request::has("misclientes") ? "style=text-decoration:underline !important;" : ""}} class="text-muted" href="?#clienteslist">Todos los clientes</a>
            |
            <a {{Request::has("misclientes") ? "style=text-decoration:underline !important;" : ""}} class="text-muted" href="?misclientes=true#clienteslist">Mis clientes</a> 
          </h6>
          <hr>
          @php
            if(Request::has("search")){
              $search = Request::get("search");
              $clientes = \App\cliente::
              whereRAW('concat(nombre," ",apat," ",amat) like "%'.$search.'%" or correo like "%'.$search.'%" or telefono like "%'.$search.'%"')
              ->limit(50)
              ->orderBy("id","desc")
              ->get();
            } else {
              $clientes = Request::has("misclientes") ? \App\cliente::
              where("agente_id",auth()->user()->id)
              ->limit(50)
              ->orderBy("id","desc")
              ->get()
                : 
              \App\cliente::
              limit(50)
              ->orderBy("id","desc")
              ->get();
            }
          @endphp
          <table id="clientes" class="table">
            <thead>
              <tr>
                <th class="text-dark">Agente</th>

                <th class="text-dark">#</th>
                <th class="text-dark">

                </th>
                <th class="text-dark">Tag</th>
                <th class="text-dark">Creado el</th>
                <th class="text-dark">Nombre</th>
                <th class="text-dark">T&eacute;lefono</th>
                <th class="text-dark">Correo</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($clientes as $c)
                    <tr>
                      <td>
                        {{($c->agente == null) ? "Sin agente": $c->agente->name}}
                      </td>
                      <td data-order="{{$c->id}}" style="text-align:center;">
                        <a href="/ventas/cliente?cid={{md5($c->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para ver">
                          UOV-{{\Carbon\Carbon::parse($c->created_at)->format("Y")}}-{{$c->id}}
                        </a>
                      </td>
                      <td>
                        @php
                          $cl = $c;
                        @endphp
                        @include('componentes.iconos')
                      </td>
                      @if($c->tags)
                      <td><span class="text-uppercase" style="color:{{$c->tags->color}};"> {{ $c->tags->tag }} </span></td>
                      <td>
                        {{$c->created_at}}
                      </td>
                      <td>
                          <a href="/ventas/cliente?cid={{md5($c->id)}}">
                            {{$c->full_name()}}
                          </a>
                          <small>
                            @if (\Carbon\Carbon::parse($c->created_at)->format("Y-m-d") == \Carbon\Carbon::now()->format("Y-m-d"))
                              <span class="badge bg-success text-white">Hoy</span>
                            @endif
                            @if (\Carbon\Carbon::parse($c->created_at)->format("Y-m-d") == \Carbon\Carbon::now()->subDays(1)->format("Y-m-d"))
                                <span class="badge bg-danger text-white">Ayer</span>
                            @endif
                          </small>
                      </td>
                      @else
                      <td><span style="color:#fff;"> Sin tags </span></td>
                      <td>
                        {{$c->created_at}}
                      </td>
                      <td><span style="color:#fff;"> Sin nombre</span></td>
                      @endif
                      <td>{{empty($c->telefono) ? "Sin teléfono" : $c->telefono}}</td>
                      <td>
                        @if (empty($c->correo))
                            Sin correo electrónico
                          @else
                            <a href="/bandeja/nuevo/enviar?a={{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                              {{mb_strtolower($c->correo)}}
                            </a>
                        @endif
                      </td>
                      
                    </tr>
                  @endforeach
            </tbody>
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
      background-color:#f6f6f6;
      border:0;
    }
    .line{
      height:2px;
      background-color:#f6f6f6;
      border:0;
      width:30%;
      margin:0;
      padding:0;
    }
    .text-muted{
      color:#BD773E !important;
    }
    .bordi{
      border:solid #03677F 1px;
      border-left:solid #03677F 8px;
      color:#03677F;
      margin-bottom:10px;
    }
    .bordiyellow{
      border:solid #ebba11 1px;
      border-left:solid #ebba11 8px;
      color: #ebba11;
    }
    .background-perfil100
    {
      height:100px;
      width:auto;
      text-align: center;
      line-height: 100px;
      font-size: 12mm;
    }
    .background-perfil
    {
      height:200px;
      width:auto;
      text-align: center;
      line-height: 200px;
      font-size: 12mm;
    }
    .background-1{
      background-color:#F59606;
    }
    .background-2{
      background-color:#03677F;
    }
    .ig{
          border-radius: 0.5rem 0px 0px 0.5rem !important;
    }
    @media only screen and (max-width: 600px) {
      .xsh {
        display: none;
      }
      .line{
        display:none;
      }
    }
  </style>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(".alumnos").DataTable(lang);
    $("#clientes2").DataTable(lang);
    $(function(){
      $(".fecha").bind("click",function(){
        $(".horamodal").modal();
      });
      $(".fb").bind("click",function(){
        $(".fbmodal").modal();
      });
      $(".formulario").bind("click",function(){
        $("#collapseExample").toggle();
      });
      $(".bus").bind("keyup",function(e){
        var k = e.keyCode;
        if(k == 13)
          $(".buscar").click();
      });
      $(".buscar").bind("click",function(){
        $(".busqueda").html("<hr><center><i class='fas fa-cog fa-spin'></i></center></hr>");
        $.post("/clientes/buscar?t="+$(".bus").val(),function(data){
          var t = $("<table>").addClass("table table-striped wrap display");
          var tr = $("<tr>");
          var td = $("<td>");
          var data = JSON.parse(data);
          $.each(data,function(i,e){
            tr = $("<tr>");
            td = $("<td>");
            td.html("<a href='/ventas/cliente?cid="+(e.cid)+"'>"+e.nombre+"</a>");
            tr.append(td);
            td = $("<td>");
            td.text(e.apat);
            tr.append(td);
            td = $("<td>");
            td.text(e.amat);
            tr.append(td);
            t.append(tr);
            tr = $("<tr>");
            td = $("<td>");
            td.text(e.correo);
            tr.append(td);
            td = $("<td>");
            td.text(e.telefono);
            tr.append(td);
            if(e.status < 4){
              td = $("<td>");
              td.html("<i class='fas fa-ban text-danger'></i> Prospecto");
              tr.append(td);
            } else {
              td = $("<td>");
              td.html("<i class='fa fa-check text-success'></i> Cliente");
              tr.append(td);
            }
            t.append(tr);
          });
          $(".busqueda").empty();
          $(".busqueda").append(t);
        });
      });

    });
  </script>
  <script>
    var table = $('#clientes').DataTable({ ...lang,
        orderCellsTop: true,
        fixedHeader: true,
        order:[1],
        initComplete: function () {
            var api = this.api();
             api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    var title = $(cell).text();
                    $(cell).html('<input type="text" placeholder="' + title + '" />');
 
                    // On every keypress in this input
                    $(
                        'input',
                        $('.filters th').eq($(api.column(colIdx).header()).index())
                    )
                        .off('keyup change')
                        .on('change', function (e) {
                            // Get the search value
                            $(this).attr('title', $(this).val());
                            var regexr = '({search})'; //$(this).parents('th').find('select').val();
 
                            var cursorPosition = this.selectionStart;
                            // Search the column for that value
                            api
                                .column(colIdx)
                                .search(
                                    this.value != ''
                                        ? regexr.replace('{search}', '(((' + this.value + ')))')
                                        : '',
                                    this.value != '',
                                    this.value == ''
                                )
                                .draw();
                        })
                        .on('keyup', function (e) {
                            e.stopPropagation();
 
                            $(this).trigger('change');
                            $(this)
                                .focus()[0]
                                .setSelectionRange(cursorPosition, cursorPosition);
                        });
                });
        },
    });
    let timer_search = null;
    @if(Request::has("search"))
      $('.dataTables_filter input').val("{{Request::get("search")}}");
    @endif
    $('.dataTables_filter input').unbind().bind("keyup", function (e) {
      e.preventDefault();
      clearInterval(timer_search);
      timer_search = setTimeout(() => {
        location.href = "/home?search="+$('.dataTables_filter input').val()+"#clienteslist";
      },300);
    });
  </script>
@endsection
