@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          @php
            $pedido = \App\orden::whereRAW("md5(id)='".Request("cid")."'")->first();
          @endphp
          <h2 class="titulo">Pedido #{{$pedido->id}}</h2>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
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
          <b>Método de pago:</b> {{($pedido->metodo == 0) ? "Pago en linea" : "Pago en efectivo"}}
        </div>
        <div class="card-body">
          <br>
          <h4 class="title">Detalles de entrega</h4>
          <p align="justify">
            <b>Nombre del cliente:</b> {{$pedido->usuario->name}}<br>
            <b>Dirección:</b> {{($pedido->direccion_id == 0) ? "Sucursal" : $pedido->direccion->direccion}}
            <br>
            <b>Correo:</b> {{$pedido->usuario->email}}
            <br>
            <b>Tel:</b> {{$pedido->usuario->telefono}}
            <br>
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
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
        <div class="row">
          <div class="col-12">
            <h3 class="titulo">Estado del pedido</h3>
            Seleccione un nuevo estado para Cambiar, el cambio se reflejará en el Shirushi Tracker del cliente.
            <form class="estado" action="/cart/actualizarestado" method="post">
              <input type="hidden" name="cid" value="{{md5($pedido->id)}}">
              @if ($pedido->status != 3 && $pedido->status != 4)
                <select class="form-control" name="status" onchange="$('.estado').submit();">
                  @foreach (\App\estado_pedido::all() as $estado)
                    <option value="{{$estado->id}}" {{($estado->id == $pedido->status) ? "selected" : ""}}>{{$estado->estado}}</option>
                  @endforeach
                </select>
                @else
                  <div class="form-control">
                    {{$pedido->estado->estado}} (Estado inmutable)
                  </div>
              @endif
            </form>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
@endsection
