@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h3 class="titulo">Pedidos</h3>
          <div class="col-12">
                @if (Auth::user()->suc->domicilio == 0)
                  Esta sucursal no tiene servicio a domicilio
                  @else
                    <table class="table table-stripped">
                      <tr>
                        <td>#</td>
                        <td>Estado</td>
                        <td>Cliente</td>
                        <td>Total</td>
                        <td>Estado</td>
                        <td></td>
                      </tr>
                    @foreach (\App\orden::where('sucursal_id',Auth::user()->suc->id)->get() as $pedido)
                      @if ($pedido->status >= 0 && $pedido->status < 4)
                        <tr>
                          <td>#S{{$pedido->id}}</td>
                          <td>
                            <span class="titulo">{{$pedido->estado->estado}}</span>
                          </td>
                          <td>
                            {{$pedido->usuario->name}}
                          </td>
                          <td>
                            {{$pedido->total}}
                          </td>
                          <td>
                            <a href="/sucursales/leer?cid={{md5($pedido->id)}}">Leer pedido</a>
                          </td>
                        </tr>
                      @endif
                    @endforeach
                  </table>
                @endif
              </div>
            </div>
          </div>
          </div>
 </div>
@endsection
