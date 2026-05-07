@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body table-responsive">
          <h3 class="titulo">Nuevos pedidos</h3>
          @if (Auth::user()->suc->domicilio == 0)
            Esta sucursal no tiene servicio a domicilio
            @else
              <table class="table">
                <tr>
                  <th># de pedido</th>
                  <th>Total</th>
                  <th>Pedido</th>
                  <th>Estado</th>
                  <th>Método de pago</th>
                </tr>
              @foreach (\App\orden::where('sucursal_id',Auth::user()->suc->id)->get() as $pedido)
                @if ($pedido->status < 3)
                  <tr>
                    <td>#S{{$pedido->id}}</td>
                    <td>${{money_format('%.2n', $pedido->total)}} MXN</td>
                    <td>
                      @php
                        date_default_timezone_set("America/Mexico_City");
                      @endphp
                      {{\Carbon\Carbon::createFromTimeStamp(strtotime($pedido->created_at))->diffForHumans()}}
                    </td>
                    <td>
                      <span class="titulo">{{$pedido->estado->estado}}</span>
                    </td>
                    <td>
                      {{($pedido->metodo == 0) ? "Pago en linea" : "Pago en efectivo"}}
                    </td>
                    <td>
                      <a class="btn btn-primary" href="/sucursales/leer?cid={{md5($pedido->id)}}">Leer pedido</a>
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
  <br>
 <div class="row">
   <div class="col-8">
     <div class="card">
       <div class="card-body">
         <h2 class="titulo">
           Últimas noticias
         </h2>
         <br>
         @php
           $gaceta = \App\gaceta::orderBy("created_at","DESC")->get();
         @endphp
         @foreach ($gaceta as $publicacion)
               <div class="col-12" style="margin-bottom:20px;">
                 <div class="card borderless">
                   <div class="card-body">
                     <a href="/{{Auth::user()->level->alias}}/gaceta?articulo={{md5($publicacion->id)}}" class="titulo">
                       <h5>{{$publicacion->titulo}}</h5>
                     </a>
                     <p class="justify">
                       {!!substr(strip_tags($publicacion->contenido),0,150)!!} ...
                     </p>
                     <small class="float-left">
                       {{$publicacion->tags}}
                     </small>
                     <small class="float-right">
                       {{\Carbon\Carbon::parse($publicacion->created_at)->diffForHumans()}}
                     </small>
                   </div>
                 </div>
               </div>
         @endforeach
       </div>
     </div>
   </div>
   <div class="col-12 col-md-6 col-lg-4 col-xl-4">
     <div class="card">
       <div class="card-body">
         <h5 class="titulo">
           Asignaciones
         </h5>
             <table class="table table-responsive">
               @php
                 $reportes = \App\reporte::where('estado_id',1)->where('level_id',Auth::user()->level->id)
                 ->orWhere('level_id',0)
                 ->orderBy("created_at","desc")->get();
               @endphp
               @if (count($reportes) > 0)
                 <div class="row">
                   @foreach ($reportes as $reporte)
                     <div class="col-12">
                       <a href="/reportes/modify/{{md5($reporte->id)}}" class="titulo">{{$reporte->nombre}}</a>
                       <p>{{$reporte->level->name}}</p>
                       <small>Prioridad <span style="color:{{$reporte->prioridad->color}};">{{$reporte->prioridad->nombre}}</span></small>
                       <p><small>{{\Carbon\Carbon::parse($reporte->created_at)->diffForHumans()}}</small></p>
                     </div>
                     <hr>
                   @endforeach
                 </div>
                 @else
               @endif
             </table>
       </div>
     </div>
   </div>
 </div>
@endsection
