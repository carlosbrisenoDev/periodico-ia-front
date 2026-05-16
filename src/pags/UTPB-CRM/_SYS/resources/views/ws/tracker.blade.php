@extends('layouts.website')
@section('style')
  <style media="screen">
  .progress-bar-animated {
  -webkit-animation: progress-bar-stripes 1s linear infinite;
  animation: progress-bar-stripes 1s linear infinite;
}

.progress{
  height: 10px;
  border-radius: 0;
  box-shadow: none;
  margin-bottom: 30px;
  overflow: visible;
}
.progress .progress-bar{
  box-shadow: none;
  position: relative;
  -webkit-animation: animate-positive 2s;
  animation: animate-positive 2s;
}
.progress .progress-bar:after{
  content: "";
  display: block;
  border: 15px solid transparent;
  border-bottom: 21px solid transparent;
  position: absolute;
  top: -26px;
  right: -12px;
}
.progress .progress-value{
  font-size: 15px;
  font-weight: bold;
  color: #000;
  position: absolute;
  top: -40px;
  right: 0;
}
.progress.pink .progress-bar:after{
  border-bottom-color: #ff4b7d;
}
.progress.green .progress-bar:after{
  border-bottom-color: #5fad56;
}
.progress.yellow .progress-bar:after{
  border-bottom-color: #e8d324;
}
.progress.blue .progress-bar:after{
  border-bottom-color: #3485ef;
}
@-webkit-keyframes animate-positive{
  0% { width: 0; }
}
@keyframes animate-positive{
  0% { width: 0; }
}
  </style>
@endsection
@section('content')
@php
  $pedido = \App\orden::whereRAW("md5(id)='".Request("cid")."'")->first();
  $progress = 33.33 * $pedido->status + 10;
  if($pedido->status == 4)
  {
    $progress = 110;
    $color = "gray";
  }
  if($pedido->status == 3){
    $color = "green";
  }
@endphp
<div class="row">
  <div class="col-12">
    <h2 class='title'>Shirushi Tracker</h2>
  </div>
  <div class="col-12">
    <br><br>
    <div class="row">
      <div class="col-12">
        <div class="progress" style="height: 5px;">
          <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{$progress}}%;{{(isset($color)) ? "background-color:".$color."!important;" : ""}}" aria-valuenow="{{$progress}}" aria-valuemin="0" aria-valuemax="110"></div>
        </div>
        <br>
        <div class="progress" style="height: 50px;">
          <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{$progress}}%;{{(isset($color)) ? "background-color:".$color."!important;" : ""}}" aria-valuenow="{{$progress}}" aria-valuemin="0" aria-valuemax="110">
            {{$pedido->estado->estado}}
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <br>
        <h4 class="title">Detalles del pedido</h4>
        <b>Orden:</b> #S{{$pedido->id}}
        <br>
        <b>Pedido creado:</b> {{\Carbon\Carbon::parse($pedido->created_at)->diffForHumans()}}
        <br>
        <b>Estado actual del pedido:</b> {{$pedido->estado->estado}}
        <br>
        <b>Total:</b> ${{money_format('%.2n', $pedido->total)}} MXN
        <br>
        <b>Dirección:</b> {{($pedido->direccion_id == 0) ? "Sucursal" : $pedido->direccion->direccion}}
        <br>
        <b>Método de pago:</b> {{($pedido->metodo == 0) ? "Pago en linea" : "Pago en efectivo"}}
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-12">
        <h4>Orden</h4>
      </div>
    @php
      $orden = json_decode($pedido->orden);
      $total = 0;
    @endphp
    @if (!empty($pedido->orden))
    @foreach ($orden as $id => $cant)
      <div class="col-md-3 col-sm-4">
        <div class="card borderless">
          @php
            $e = \App\platillo::find($id);
          @endphp
          @if ($e != null)
              <img src="/imagenes/watchar/{{md5($e->imagenes[0]->imagen_id)}}" height="200px" class="card-img-top">
              <div class="card-body">
                <div class="h150">
                  <h3>{{substr($e->nombre,0,15)}}</h3>
                  <p>
                    <b>Precio por unidad:</b> ${{money_format('%.2n', $e->precio)}}
                    <br>
                    <b>Cantidad: </b> {{$cant->cantidad}}
                  </p>
                </div>
                <input type="hidden" name="cid" value="{{$id}}">
                <hr>
                <p>
                  <b>Subtotal:</b> ${{money_format('%.2n', $e->precio * $cant->cantidad)}} MXN
                  @php
                    $total += $e->precio * $cant->cantidad;
                  @endphp
                </p>
              </div>
          @else
                Elemento no encontrado
          @endif
        </div>
      </div>
    @endforeach
  @endif

  </div>
  <br>
    <div class="row">
      <div class="col-12">
        <h4 class="title">Historial</h4>
        <table class="table table-stripped">
          @foreach (explode("<br>",$pedido->historial) as $tag)
            @if (strstr($tag,"|"))
              <tr>
              @php
                $data = explode("|",$tag);
              @endphp
              <td>
                {{\Carbon\Carbon::parse($data[0])->diffForHumans()}}
              </td>
              <td>
                {{$data[1]}}
              </td>
            </tr>
            @endif
          @endforeach
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
