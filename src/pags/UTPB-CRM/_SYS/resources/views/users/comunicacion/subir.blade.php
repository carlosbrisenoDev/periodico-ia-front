@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                  <div class="clearfix">
                    <div class="pull-left">
                      <h4><a href="javascript:window.history.back();">Gestor de descargas</a></h4>
                    </div>
                    <div class="pull-right">
                    </br>
                    </div>
                  </div>
                  <hr>
                    <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="/papeleria/save">
                      <input type="hidden" name="id" value="{{$formato->id}}">
                      {{ csrf_field() }}
                    <div class="col-md-12 nopadding">
                      <div class="col-md-12 nopadding">
                        @if ($formato->archivo == 1)
                            <div class="col-md-2">
                              <div class="thumbnail topp">
                                <div class="img img-responsive text-center">
                                  <i class="{{$formato->fa()}}"></i>
                                </div>
                                <span class="texto">{{str_replace("."," ",$formato->asunto)}}</span>
                                <div class="clearfix">
                                  <div class="col-md-3 nopadding text-center">
                                  </div>
                                  <div class="col-md-3 nopadding text-center">
                                    <a href="/papeleria/download/{{md5($formato->id)}}" class="btn btn-default"><i class="fa fa-download"></i></a>
                                  </div>
                                  <div class="col-md-3 nopadding text-center">
                                    <a target="_blank" href="/papeleria/watchar/{{md5($formato->id)}}" class="btn btn-default"><i class="fa fa-eye"></i></a>
                                  </div>
                                  <div class="col-md-3 nopadding text-center">
                                  </div>
                                </div>
                              </div>
                            </div>
                          @else
                            <div class="col-md-12">
                              <h4>No hay documentos</h4>
                            </div>
                        @endif
                      </div>
                      @if ($formato->archivo == 0)
                      <div class="col-md-12">
                        <hr>
                        <input type="file" name="documento[]" placeholder="Seleccione los documentos">
                      </div>
                      @endif
                    </div>
                    @if ($formato->archivo == 0)
                    <div class="col-md-12 nopadding">
                      <hr>
                      <div class="col-md-3">
                        <button type="submit" class="btn btn-primary large">
                        <i class="fa fa-upload"></i>    Subir documento
                        </button>
                      </form>
                      </div>
                      <div class="col-md-3">

                      </div>
                    </div>
                  @endif
                </div>
            </div>
        </div>
@endsection
