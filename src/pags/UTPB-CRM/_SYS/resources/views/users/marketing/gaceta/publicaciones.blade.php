@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                    <div class="row">
                      <div class="col">
                        <div class="float-left">
                          <h3>Publicaciones</h3>
                        </div>
                        <div class="float-right">
                          <a class="btn btn-primary" href="/gaceta/nuevo/news">
                            <i class="fa fa-plus"></i>
                            Nuevo
                          </a>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <div class="row">
                      @foreach ($gaceta as $publicacion)
                            <div class="col-12 col-md-6 col-lg-4 col-xl-4" style="margin-bottom:20px;">
                              <div class="card">
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
                                <div class="row">
                                  <div class="col-12" style="">
                                    <a href="/gaceta/editar/{{md5($publicacion->id)}}" class="float-left">
                                      <i class="fa fa-edit"></i> Editar
                                    </a>
                                    <a href="/gaceta/delete/{{md5($publicacion->id)}}" class="titulo float-right">
                                      <i class="fa fa-trash"></i> Eliminar
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                      @endforeach
                    </div>
                </div>
            </div>
        </div>
@endsection
