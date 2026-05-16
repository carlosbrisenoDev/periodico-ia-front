@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card table-responsive">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-left">
              <h5 class="card-title">Clientes</h5>
              <h6 class="card-subtitle mb-2 text-muted">Todos los clientes</h6>
            </div>
            <div class="float-right">
              <input type="text" class="form-control  buscar" placeholder="Buscar ...">
            </div>
          </div> 
          <hr>
            <table id="clientes" class="table table-sm table-striped table-hover">
              <thead>
                <tr>
                  <th class="text-dark">Agente</th>

                  <th class="text-dark">#</th>
                  <th class="text-dark">

                  </th>
                  <th class="text-dark">Tag</th>
                  <th class="text-dark">Nombre</th>
                  <th class="text-dark">Paterno</th>
                  <th class="text-dark">Materno</th>
                  <th class="text-dark">T&eacute;lefono</th>
                  <th class="text-dark">Correo</th>
                </tr>
              </thead>
              <tbody>
                @php
                    $tel = Request::get("phone") ? str_replace("+","",Request::get("phone")) : "";
                    $clientes = Request::has("phone") ? \App\cliente::orderBy("created_at","DESC")->where("telefono",$tel)->get()
                    : \App\cliente::orderBy("created_at","DESC")->get();
                @endphp
                @foreach ($clientes as $c)
                  <tr>
                    <td>
                      {{($c->agente == null) ? "Sin agente": $c->agente->name}}
                    </td>
                    <td style="text-align:center;">
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
                    <td><span style="color:{{$c->tags->color}};"> {{ $c->tags->tag }} </span></td>
                    <td><span style="color:{{$c->tags->color}};"> {{empty($c->nombre) ? "Sin nombre" : $c->nombre}} </span></td>
                    @else
                    <td><span style="color:#fff;"> Sin tags </span></td>
                    <td><span style="color:#fff;"> Sin nombre</span></td>
                    @endif
                    <td>{{empty($c->apat) ? "Sin apat" : $c->apat}}</td>
                    <td>{{empty($c->amat) ? "Sin amat" : $c->amat}}</td>
                    <td>{{empty($c->telefono) ? "Sin teléfono" : $c->telefono}}</td>
                    <td>
                      @if (empty($c->correo))
                          Sin correo electrónico
                        @else
                          <a href="/bandeja/nuevo/enviar?a={{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                            {{$c->correo}}
                          </a>
                      @endif
                    </td>
                    
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <th>Agente</th>
                  <th>#</th>
                  <th>
  
                  </th>
                  <th>Tag</th>
                  <th>Nombre</th>
                  <th>Paterno</th>
                  <th>Materno</th>
                  <th>T&eacute;lefono</th>
                  <th>Correo</th>
                </tr>
            </tfoot>
            </table>
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
    // $(document).ready(function() {
    //     // Setup - add a text input to each footer cell
    //     $('#clientes tfoot th').each( function (i) {
    //       console.log('a')
    //       var title = $('#clientes thead th').eq( $(this).index() ).text();
    //       $(this).html( '<input type="text" placeholder="'+title+'" data-index="'+i+'" />' );
    //     });
      
    //     DataTable
    //     var table = $('#clientes').DataTable( {
    //       scrollY:        "800px",
    //       scrollX:        true,
    //       scrollCollapse: true,
    //       paging:         false,
    //       fixedColumns:   true
    //     });
    
    //     // Filter event handler
    //     $( table.table().container() ).on( 'keyup', 'tfoot input', function () {
    //       table
    //           .column( $(this).data('index') )
    //           .search( this.value )
    //           .draw();
    //     });
    // });
    $(document).ready(function () {
    // Setup - add a text input to each footer cell
    $('#clientes thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo('#clientes thead');
 
    var table = $('#clientes').DataTable({
        orderCellsTop: true,
        fixedHeader: true,
        initComplete: function () {
            var api = this.api();
 
            // For each column
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
});
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
