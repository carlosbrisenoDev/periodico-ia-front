@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-start">
              <h5 class="card-title">Suscripciones de correo automáticas</h5>
              <h6 class="card-subtitle mb-2 text-muted">Lista de suscripciones</h6>
              <p>Una suscripción automatica es una lista enlazada de iBrochures que se envian al cliente cada determinado tiempo.</p>
            </div>
            <div class="float-end">
            </div>
          </div>
          <hr>
          <table class="table table-sm table-striped table-hover">
            <thead>
              <td>Folio</td>
              <td>Título</td>
              <td>Brochures</td>
              <td>Acciones</td>
              <td>Creada el</td>
            </thead>
            <tbody>
              @foreach (\App\suscripciones::orderBy("created_at","DESC")->get() as $c)
                <tr>
                  <td>{{$c->id}}</td>
                  <td>
                    <a href="/ventas/crearsuscripcion?cid={{md5($c->id)}}">
                      {{$c->titulo}}
                    </a>
                  </td>
                  <td>
                    {{$c->ibrochures->count()}}
                  </td>
                  <td>
                    <small>
                      
                    </small>
                  </td>
                  <td>
                    {{$c->created_at}}
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
              text : "iBrochures",
              action: function ( e, dt, node, config ) {
                location.href = "/ventas/gacetadeenvio";
              }
            },
            {
                text: 'Nueva suscripción',
                action: function ( e, dt, node, config ) {
                    Swal.fire({
                      "title" : "Nueva Suscripción",
                      "text" : "Ingresa el título de la suscripción",
                      "icon" : "info",
                      "input" : "text",
                      "inputAttributes" : {
                        "required" : true,
                        "placeholder" : "Título"
                      },
                      "showConfirmButton" : true,
                      "showCancelButton" : true,
                      "confirmButtonText" : "Guardar",
                      "cancelButtonText" : "Cancelar",
                      showLoaderOnConfirm: true,
                      preConfirm: (input) => {
                        return fetch(`/suscripciones/create`,{
                          method: "POST",
                          body: JSON.stringify({
                            "titulo" : input,
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
                        location.href = "/ventas/crearsuscripcion?cid="+result.value.cid;
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
