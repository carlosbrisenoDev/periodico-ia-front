@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  @php
    $show = '';
    if(auth()->user()->levels->alias=='administrador'){
      $show = '';
    }
    elseif(auth()->user()->levels->alias=='ventas'){
      $show = 'd-none';
    }
    

  @endphp
<div class="content mt-5">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center text-black">#</th>
                    <th class=" text-black">Name</th>
                    <th class="text-black">Tipo</th>
                    <th class="text-black">Costo</th>
                    {{-- <th class="text-right">Costo Total</th> --}}
                    <th class="text-black">Precio</th>
                    <th class="text-black">Descuento Max</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\productos::where('deleted_at',null)->get() as $producto)
                    <tr>
                        <td class="text-center">{{$producto->id}}</td>
                        <td>{{$producto->nombre}}</td>
                        <td>{{$producto->tipo}}</td>
                        <td>{{$producto->costo}}</td>
                        <td>{{$producto->precio}}</td>
                        <td>{{$producto->descuento_max}}</td>
                        {{-- <td class="text-right">&euro; 99,225</td> --}}
                        <td class="td-actions text-right">
                            {{-- <a type="button" rel="tooltip" class="btn btn-info" href="#">
                                <i class="material-icons">Ver</i>
                            </a> --}}
                            <a type="button" rel="tooltip" class="btn btn-success" href="{{url('/productos/edit/'.$producto->id)}}">
                                <i class="material-icons">edit</i>
                            </a>
                            <form action="{{url('/productos/destroy')}}" method="POST">
                                @csrf
                                <input type="hidden" name="prod_id" value="{{$producto->id}}">
                                <button type="submit" rel="tooltip" class="btn btn-danger">
                                    <i class="material-icons">Borrar</i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
  </div>
  @endsection