@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                    <h3>Módulo</h3>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/levels/update">
                    <div class="col-md-6">
                          {{ csrf_field() }}

                          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                              <label for="name" class="col-md-4 control-label">Nombre del área</label>
                              <input type="hidden" name="id" value="{{$level->id}}">
                              <div class="col-md-6">
                                  <input placeholder="Nombre del área" disabled id="name" type="text" class="form-control large" name="name" value="{{$level->name}}" required autofocus>
                              </div>
                          </div>

                          <div class="col-md-12">
                            <label for="name" class="col-md-4 control-label">Módulos instalados</label>
                            <div class="col-md-6">
                              <div class="list-group">
                                @php

                                $d = dir("/home/unisanto/sii.unisantorizaba.com/_SYS/resources/views/users");

                                while (false !== ($entry = $d->read())) {
                                  if(!strpos($entry,".") && $entry != "." && $entry != ".."){
                                    @endphp
                                      <div>
                                        @if($entry == $level->alias)
                                          <input type="radio" id="{{$entry}}" value="{{$entry}}" name="alias" selected checked>
                                        @else
                                          <input type="radio" id="{{$entry}}" value="{{$entry}}" name="alias">
                                        @endif
                                        <label for="{{$entry}}">
                                          <i class='fa fa-puzzle-piece'></i> {{strtoupper($entry)}}
                                        </label>
                                      </div>
                                    @php
                                  }
                                }
                                $d->close();
                                @endphp
                              </div>
                            </div>
                          </div>
                      <div class="form-group">
                          <div class="col-md-6 col-md-offset-4">
                              <button type="submit" class="btn btn-primary large">
                              <i class="fa fa-save"></i>    Actualizar
                              </button>
                          </div>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
@endsection
