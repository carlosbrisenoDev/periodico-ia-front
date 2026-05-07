@extends('layouts.website')
@section('content')
<div class="row">
  <div class="col-12">
    <h2 class="title">
      <div class="h100">
        MI PEDIDO
      </div>
    </h2>
  </div>
</div>
@php
  $total = 0;
@endphp
    @if (count(Session::get("cart")) > 0)

      <div class="row">
      @foreach (Session::get("cart") as $id => $articulo)
        <div class="col-md-3 col-sm-4">
          <div class="card borderless">
            <form class="" action="/cart/del" method="post">
              <input type="hidden" name="cid" value="{{$id}}">
              <button class="close-btn">
                <i class="fa fa-trash"></i>
              </button>
            </form>
            @php
              $e = \App\platillo::find($id);
            @endphp
            @if ($e != null)
              <form class="" action="/cart/modify" method="post">
                <img src="/imagenes/watchar/{{md5($e->imagenes[0]->imagen_id)}}" height="200px" class="card-img-top">
                <div class="card-body">
                  <div class="h150">
                    <h3>{{substr($e->nombre,0,15)}}</h3>
                    <p>
                      <b>Precio por unidad:</b> ${{money_format('%.2n', $e->precio)}}
                    </p>
                  </div>
                  <input type="hidden" name="cid" value="{{$id}}">
                  <div class="input-group">
                    <input type="number" class="form-control" max="100" min="1" name="cantidad" value="{{$articulo["cantidad"]}}">
                    <div class="input-group-append">
                      <button class="btn save-btn" type="submit" name="" value="">
                        <i class="fa fa-save"></i>
                      </button>
                    </div>
                  </div>
                  <hr>
                  <p>
                    <b>Subtotal:</b> ${{money_format('%.2n', $e->precio * $articulo["cantidad"])}} MXN
                    @php
                      $total += $e->precio * $articulo["cantidad"];
                    @endphp
                  </p>
                </div>
              </form>
            @else
                  Elemento no encontrado
            @endif
          </div>
        </div>
      @endforeach
    </div>
    <hr>
    <div class="row">
      <div class="col-12">
        <b>Subtotal:</b>
        <p align="right">${{money_format('%.2n', $total)}} MXN</p>
        <b>Total:</b>
        <p align="right">${{$total=money_format('%.2n', $total)}} MXN</p>
      </div>
    </div>
    <hr>
    <form class="" action="/cart/payment" method="post">
    <div class="row">
      <div class="col-12">
        <h3 class="title h100">
          Selecciona la dirección de entrega
        </h3>
        <div class="row">
          <div id="demo">

          </div>
          @foreach (Auth::user()->direcciones as $direccion)
            <div class="col-3">
              <div class="card direc hidden" lat="{{$direccion->lat}}" lng="{{$direccion->lng}}">
                <div class="card-body">
                  <div class="text-center">
                    <i class="fas fa-map-marker-alt fa-3x"></i>
                    <div class="h100">
                      {{$direccion->nombre}}
                      <p>
                        {{$direccion->direccion}}
                        <br>
                        <input type="radio" autocomplete="off" id="dir{{$direccion->id}}" name="direccion_id" value="{{$direccion->id}}">
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="row">
          <div class="col-12">
            <br><br>
            <a href="/">Mis direcciones</a><br>
            <hr>
          </div>
          <div class="col-12">
            <h3>Sucursal de entrega más cercana:</h3> <span class="sucursal">Selecciona la dirección de entrega para buscar tu sucursal más cercana</span>
          </div>
        </div>
      </div>
    </div>
    <hr>
      <div class="row">
        <div class="col-12">
          <input type="checkbox" id="monedero" name="monedero" value="1">
          <label for="monedero">Usar ${{money_format('%.2n', Auth::user()->cash)}} MXN de mi monedero.</label>
          <table border="0" cellpadding="10" cellspacing="0" align="center">
            <tr>
              <td align="center"></td>
            </tr>
            <tr>
              <td align="center">
                <a href="https://www.paypal.com/in/webapps/mpp/paypal-popup" title="How PayPal Works" onclick="javascript:window.open('https://www.paypal.com/in/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;">
                  <img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-200px.png" border="0" alt="PayPal Logo">
                </a>
                <br><br>
                <input type="hidden" name="sucid" class="sucid" value="">
                <div class="">
                  <button type="submit" name="formapago" value="efectivo" class="btn btn-primary pagar hidden" disabled>
                    <i class="fab fa-cash"></i> Pagar al recibir (Solo en efectivo) $<span class="totali">{{money_format('%.2n', $total)}}</span> MXN
                  </button>
                  <hr>
                  <button type="submit" name="formapago" value="paypal" class="btn btn-primary pagar hidden" disabled>
                    <i class="fab fa-paypal"></i> Pagar con PayPal $<span class="totali">{{money_format('%.2n', $total)}}</span> MXN
                  </button>
                </div>
              </td>
            </tr>
          </table>
        </div>
      </div>

    </form>
    @else
          <div class="col-12">
            <h3>
              No hay elementos en tu pedido, visita nuestro <a href="/shirushi/menu">menú</a> para empezar.
            </h3>
          </div>
    @endif
@endsection
@section('scripts')
  <script src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&sensor=false&key=AIzaSyB3Boc8zi4Mu6KT_ONs2Jq48OeZmcVzSrM"></script>
  <script type="text/javascript">
    $(function(){
      $("#monedero").bind("change",function(){
        if($(this).prop("checked")){
          $(".totali").text("{{money_format('%.2n', $total-Auth::user()->cash)}}");
        } else{
          $(".totali").text("{{money_format('%.2n', $total)}}");
        }
      });
    });
    $("input[type='radio']").removeAttr("checked");
    $(".direc").bind("click",function(){
      this.querySelector("input").click();
      find_closest_marker({
        lat:parseFloat($(this).attr("lat")),
        lng:parseFloat($(this).attr("lng")),
      },
        function(pos,place,d){
          var dis = Math.round((d/100)/10);
          var max = 5;
          if(dis > max){
            $(".sucursal").text("Lo sentimos, no hay una sucursal que pueda atender tu orden, la sucursal más próxima se encuentra a "+dis+"km de tu ubicación actual, nuestra área de servicio es de "+max+" km.");
            $(".pagar").attr("disabled","disabled").addClass("disabled hidden");
            $(".sucid").val("");
          } else {
            $(".sucursal").text(place.nombre+ "("+dis+" km)");
            $(".sucid").val(place.id);
            $(".pagar").removeAttr("disabled").removeClass("disabled hidden");
          }

        }
      );
    });
      // In the following example, markers appear when the user clicks on the map.
      // Each marker is labeled with a single alphabetical character.
      var labels = "Yo";
      var map;
      var labelIndex = 0;
      var current;
      var x = document.getElementById("demo");
      var markers = [];
      var sucursales = {!!\App\sucursal::select(["id","nombre","alias","lat","lng"])->where("visible",1)->where("domicilio",1)->get()->toJSON()!!};
      function getLocation() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(showPosition);
        } else {
          x.innerHTML = "Activa la Ubicación para seleccionar el destino de entrega.";
        }
      }

      function showPosition(position) {
        current = {lat:position.coords.latitude,lng:position.coords.longitude};
        initialize();
      }
      function initialize() {

        for(var suc in sucursales){
          console.log(sucursales[suc]);
          setMarker({
            lat:parseFloat(sucursales[suc].lat),
            lng:parseFloat(sucursales[suc].lng)
          },sucursales[suc].nombre,map);
        }

        $(".direc").removeClass("hidden");
      }

      function setMarker(location,l, map) {
        markers.push(new google.maps.Marker({
          map: map,
          title: l,
          position: location
        })
      );
      }

      google.maps.event.addDomListener(window, 'load', getLocation);


  function find_closest_marker(latlng, callback ) {

    var lat, lng, pos;
    var closestMarker = -1;
    var closestDistance = Number.MAX_VALUE;
    var geocoder = new google.maps.Geocoder();
    var closesSuc = null;
    geocoder.geocode( { 'location': latlng}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        lat = results[0].geometry.location.lat();
        lng = results[0].geometry.location.lng();
        pos = new google.maps.LatLng(lat, lng);

          for( i = 0; i< markers.length; i++ ) {
              var distance = google.maps.geometry.spherical.computeDistanceBetween(markers[i].getPosition(), pos);
              if ( distance < closestDistance ) {
                  closestMarker = i;
                  closestDistance = distance;
                  closesSuc = sucursales[i];
              }
          }

          lat = markers[closestMarker].position.lat();
          lng = markers[closestMarker].position.lng();
          pos = new google.maps.LatLng(lat, lng);
          callback(pos,closesSuc,closestDistance);

        }
    });
  } // find_closest_marker

  function calculate_route(lat, lng) {
    var start, end;

    start = document.getElementById('start').value;

    if (!lat && !lng) {
      find_closest_marker(start, function(end) {
        var request = {
          origin: start,
          destination: end,
          travelMode: google.maps.TravelMode.DRIVING
        };
        directionsService.route(request, function(response, status) {
          if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
          }
        });
      });
    }

    else {
      end = lat + ',' + lng;
        var request = {
          origin: start,
          destination: end,
          travelMode: google.maps.TravelMode.DRIVING
        };
        directionsService.route(request, function(response, status) {
          if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
          }
        });
    }
  }
  </script>
@endsection
