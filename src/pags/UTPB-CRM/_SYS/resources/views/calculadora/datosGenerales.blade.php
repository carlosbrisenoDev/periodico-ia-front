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
  <div class="card">
    <div class="card-body">
      {{-- <button class="btn btn-info" id="report-generate">Imprimir / Generar Reporte</button> --}}
      <button class="btn btn-warning">Ver mis calculos/tickets</button>
      @if(auth()->user()->levels->alias=='administrador')
        <button class="btn btn-danger">Ver calculos/tickets Generados por otros usuarios</button>
      @endif
    </div>
  </div>

  <div class="content mt-5">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center text-black">#</th>
                    <th class=" text-black">Name</th>
                    <th class="text-black">Rol</th>
                    <th class="text-black">Fecha</th>
                    {{-- <th class="text-right">Costo Total</th> --}}
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\registros_calculadora::where('deleted_at',null)->get() as $registro)
                    <tr>
                        <td class="text-center">{{$registro->id}}</td>
                        <td>{{$registro->usuario->name}}</td>
                        <td>{{$registro->usuario->levels->alias}}</td>
                        <td>{{$registro->created_at}}</td>
                        {{-- <td class="text-right">&euro; 99,225</td> --}}
                        <td class="td-actions text-right">
                            <a type="button" rel="tooltip" class="btn btn-info" href="{{url('/calculadora/registros/registro/'.md5($registro->id))}}">
                                <i class="material-icons">Ver</i>
                            </a>
                            {{-- <button type="button" rel="tooltip" class="btn btn-success">
                                <i class="material-icons">edit</i>
                            </button> --}}
                            <button type="button" rel="tooltip" class="btn btn-danger">
                                <i class="material-icons">Borrar</i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
  </div>
  @endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection