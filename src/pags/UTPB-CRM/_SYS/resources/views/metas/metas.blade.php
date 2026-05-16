@extends('users.' . Auth::user()->level->alias . '.home')
@section('styles')

@endsection
@section('scripts')

@endsection
@section('content')
@php
  $meta = \App\metas::whereRAW("month(created_at)='".date("m")."'")->first();
  $meses = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
@endphp
<h4>Metas para el mes de {{$meses[date("m")*1]}}</h4>
<form action="{{url('/metas/setmeta')}}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($meta)
        <input type="hidden" class="form-control" required name="metaid" value="{{md5($meta->id)}}">
    @endif
    <div class="form-row row">
        <div class="form-group col-md-6">
            <label for="metam">Meta Mensual</label>
            @if($meta)
                <input type="number" class="form-control" id="metam" placeholder="Meta Mensual" required name="metam" value="{{$meta->meta_mensual}}">
            @else
                <input type="number" class="form-control" id="metam" placeholder="Meta Mensual" required name="metam">
            @endif
        </div>
        <div class="form-group col-md-6">
            <label for="metat">Meta Total</label>
            @php
                $meta_total = \App\metas::where('meta_total','!=',null)->orderBy('created_at','DESC')->first();
            @endphp
            @if($meta)
                @if($meta_total)
                    <input type="number" class="form-control" id="metat" placeholder="Meta Total" required name="metat" value="{{$meta_total->meta_total}}">
                @else
                    <input type="number" class="form-control" id="metat" placeholder="Meta Total" required name="metat" value="{{$meta->meta_total}}">
                @endif
            @else
                @if($meta_total)
                    <input type="number" class="form-control" id="metat" placeholder="Meta Total" required name="metat" value="{{$meta_total->meta_total}}">
                @else
                    <input type="number" class="form-control" id="metat" placeholder="Meta Total" required name="metat">
                @endif
                
            @endif
        </div>
    </div>
    <div class="form-row row">
        <div class="form-group col-md-12">
            <label for="equilibrio">Meta para Equilibrio</label>
            @if($meta)
                <input type="number" class="form-control" id="metam" placeholder="Meta para equilibrio" required name="equilibrio" value="{{$meta->equilibrio}}">
            @else
                <input type="number" class="form-control" id="metam" placeholder="Meta para equilibrio" required name="equilibrio">
            @endif
        </div>
    </div>
    <div class="form-row row">
        <div class="col-2">
            <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
    </div>
</form>
@endsection
