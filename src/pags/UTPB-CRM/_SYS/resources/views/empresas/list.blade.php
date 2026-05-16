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
                    <th class="text-black">Slug</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\empresas::where('deleted_at',null)->get() as $empresa)
                    <tr>
                        <td class="text-center">{{$empresa->id}}</td>
                        <td>{{$empresa->nombre}}</td>
                        <td>{{$empresa->slug}}</td>
                        
                        <td class="td-actions text-right">
                            <a type="button" rel="tooltip" class="btn btn-success" href="{{url('/empresas/edit/'.$empresa->id)}}">
                                <i class="material-icons">edit</i>
                            </a>
                            <form action="{{url('/empresas/destroy')}}" method="POST">
                                @csrf
                                <input type="hidden" name="empr_id" value="{{$empresa->id}}">
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
