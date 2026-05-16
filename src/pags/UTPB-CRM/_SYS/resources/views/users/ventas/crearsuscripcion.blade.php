@extends('users.'.Auth::user()->level->alias.'.home')
@php
    $suscripcion = \App\suscripciones::whereRAW("md5(id)='".Request::get("cid")."'")->first();
@endphp
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-start">
              <h5 class="card-title">Editor de suscripciones</h5>
              <h6 class="card-subtitle mb-2 text-muted">{{$suscripcion->titulo}}</h6>
            </div>
            <div class="float-end">
            </div>
          </div>
          <hr>
          <table class="table table-sm table-striped table-hover">
            <thead>
              <td>Folio</td>
              <td>Título</td>
              <td>Disparo trás</td>
              <td>Etiquetas</td>
              <td></td>
              <td>Creada el</td>
            </thead>
            <tbody>
              @foreach ($suscripcion->ibrochures as $si)
                @php
                    $item = $si->ibrochure;
                @endphp
                <tr>
                  <td>{{$item->id}}</td>
                  <td>
                      {{$item->titulo}}.{{$item->asunto}}
                  </td>
                  <td data-sort="{{$si->lauch_at}}">
                    {{$si->launch_at}} Minutos
                  </td>
                  <td>
                    @foreach (json_decode($item->grupos) as $item2)
                        <span class="tag label label-info">{{$item2}}</span>
                    @endforeach
                  </td>
                  <td>
                    <small>
                        <a class="quitar" cid="{{md5($si->id)}}" href="#">Quitar</a>
                    </small>
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
      order: [[2, 'desc']],
      buttons:
        [ 
            ... lang.buttons,
            {
              text : "Suscripciones",
              action: function ( e, dt, node, config ) {
                location.href = "/ventas/suscripciones";
              }
            },
            {
                text: 'Agregar iBrochure',
                action: function ( e, dt, node, config ) {
                    Swal.fire({
                      "title" : "Selecciona el iBrochure",
                      "html" : "Selecciona el iBrochure deseado y trás cuantos minutos será enviado</br>" +
                                `<label>iBrochure:</label></br>`+
                                `<select class="swal2-select m-0 mb-2 mt-2" id="ibrochure_id">
                                    @foreach(\App\gaceta::all() as $item)
                                        <option value="{{$item->id}}">{{$item->titulo}}.{{$item->asunto}}.{{$item->tags}}</option>
                                    @endforeach
                                </select>`+
                                `<label>Enviar trás minutos:</label></br>`+
                                `<input class="swal2-input m-0" id="minutos" min="0" value="10" type="number" />`,
                      "icon" : "info",
                      "showConfirmButton" : true,
                      "showCancelButton" : true,
                      "confirmButtonText" : "Guardar",
                      "cancelButtonText" : "Cancelar",
                      showLoaderOnConfirm: true,
                      preConfirm: () => {
                        let iBrochure_id = $("#ibrochure_id").val();
                        let minutos = $("#minutos").val();
                        if(iBrochure_id == "" || minutos == ""){
                            Swal.showValidationMessage(
                              `Debes seleccionar un iBrochure y los minutos`
                            )
                            return false;
                        }
                        return fetch(`/suscripciones/addiBrochure`,{
                          method: "POST",
                          body: JSON.stringify({
                            "ibrochure_id" : iBrochure_id,
                            "minutos" : minutos,
                            "suscripcion_id" : {{$suscripcion->id}},
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
                        location.reload();
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

    $(".quitar").bind("click", () => {
      let me = event.target;
      let cid = $(me).attr("cid");
      Swal.fire({
        "title" : "¿Quitar brochure?",
        "text" : "Sí quitas el iBrochure, podrás agregarlo más adelante",
        "icon" : "warning",
        "showConfirmButton" : true,
        "showCancelButton" : true,
        "confirmButtonText" : "Continuar",
        "cancelButtonText" : "Cancelar",
        showLoaderOnConfirm: true,
        preConfirm: (input) => {
          return fetch(`/suscripciones/removeiBrochure`,{
            method: "POST",
            body: JSON.stringify({
              "ibrochure" : cid,
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
            location.reload();
            Swal.fire(
                '¡Excelente!',
                'El correo ha sido enviado',
                'success'
            )
        }
      });
    });
  </script>
@endsection
