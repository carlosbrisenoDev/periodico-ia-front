@extends('users.'.Auth::user()->level->alias.'.home')
@php
    $cs = \App\clientes_suscripciones::whereRAW("md5(id)='".Request::get("cid")."'")->first();
    $suscripcion = $cs->suscripcion;
    $cliente = $cs->cliente;
@endphp
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-start">
              <h5 class="card-title">Suscripción de cliente</h5>
              <h6 class="card-subtitle mb-2 text-muted">Suscripción del cliente</h6>
              <a href="/ventas/cliente?cid={{md5($cs->cliente_id)}}"></a>
            </div>
            <div class="float-end">
            </div>
          </div>
          <hr>
          <table class="table table-sm table-striped table-hover">
            <thead>
              <td>Folio</td>
              <td>Título</td>
              <td>Enviar el</td>
              <td>Estado</td>
              <td>Creada el</td>
            </thead>
            <tbody>
              @foreach ($cs->cliente_suscripciones_ibrochures as $si)
                @php
                    $item = $si->ibrochure;
                @endphp
                <tr>
                  <td>{{$item->id}}</td>
                  <td>
                      {{$item->titulo}}.{{$item->asunto}}
                  </td>
                  <td data-sort="{{$si->expire_at}}" class="{{\Carbon\Carbon::createFromTimestamp($si->expire_at)->lt(\Carbon\Carbon::now()) ? "text-danger" : ""}}">
                    {{\Carbon\Carbon::createFromTimestamp($si->expire_at)->toDateTimeString()}}
                  </td>
                  <td>
                    {{$si->status == 0 ? "En espera" : "Enviado"}}
                  </td>
                  <td>
                    {{$item->created_at}}
                  </td>
                </tr>
              @endforeach
            </tbody>
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
    .text-muted{
      color:#BD773E !important;
    }
  </style>
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
@endsection
@section('scripts')
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
  <script>
    $(".table").DataTable({
      ...lang,
      buttons:
        [ 
            ... lang.buttons,
            {
              text : "Cliente",
              action: function ( e, dt, node, config ) {
                location.href = "/ventas/cliente?cid={{md5($cliente->id)}}";
              }
            },
            {
                text: 'Eliminar suscripción',
                action: function ( e, dt, node, config ) {
                    Swal.fire({
                      "title" : "¿Desea eliminar la suscripción del cliente?",
                      "text" : "La suscripción será eliminada aúnque no haya sido concluída",
                      "icon" : "info",
        
                      "showConfirmButton" : true,
                      "showCancelButton" : true,
                      "confirmButtonText" : "Continuar",
                      "cancelButtonText" : "Cancelar",
                      showLoaderOnConfirm: true,
                      preConfirm: (input) => {
                        return fetch(`/suscripciones/deleteCliente`,{
                          method: "POST",
                          body: JSON.stringify({
                            "csid" : "{{md5($cs->id)}}",
                            "_token" : $("meta[name=csrf-token]").attr("content")
                          }),
                          headers: {"Content-type": "application/json; charset=UTF-8"}
                        })
                          .then(response => {
                            if (!response.ok) {
                              throw new Error(response.statusText)
                            }
                            return response.json()
                          })
                          .catch(error => {
                            Swal.showValidationMessage(
                              `Request failed: ${error}`
                            )
                          })
                      },
                      allowOutsideClick: () => !Swal.isLoading()
                    }).then(result => {
                      console.log(result);
                      if(result.isConfirmed){
                        location.href = "/ventas/cliente?cid={{md5($cliente->id)}}";
                      }
                    });
                }
            }
        ],
        paging:false
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
