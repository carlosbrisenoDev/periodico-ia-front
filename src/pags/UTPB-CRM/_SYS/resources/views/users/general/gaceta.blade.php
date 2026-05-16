@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="row">
          <div class="col-8">
            <div class="card card-default">
                <div class="card-body">
                  @php
                    $pub = \App\gaceta::whereRAW("md5(id)='".$_REQUEST["articulo"]."'")->first();
                  @endphp
                      <h1 class="titulo">
                        {{$pub->titulo}}
                      </h1>
                      <small class="float-right">
                        {{\Carbon\Carbon::parse($pub->created_at)->diffForHumans()}}
                      </small>
                      <br>
                      <hr>
                      <div class="card-body">
                        {!! $pub->contenido !!}
                      </div>
                    </div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-lg-4 col-xl-4">
            <div class="row">
              @php
                $gaceta = \App\gaceta::orderBy("created_at","DESC")->limit(3)->get();
              @endphp
              @foreach ($gaceta as $publicacion)
                    <div class="col-12" style="margin-bottom:20px;">
                      <div class="card">
                        <div class="card-body">
                          <a href="/{{Auth::user()->level->alias}}/gaceta?articulo={{md5($publicacion->id)}}" class="titulo">
                            <h5>{{$publicacion->titulo}}</h5>
                          </a>
                          <p class="justify">
                            {{substr(strip_tags($publicacion->contenido),0,150)}} ...
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
@endsection
