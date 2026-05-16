@extends('users.'.Auth::user()->level->alias.'.home')
@php
  $gaceta = \App\gaceta::whereRAW("md5(id)='".Request::get("cid")."'")->first();
@endphp
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-start">
              <h5 class="card-title">iBrochure</h5>
              <h6 class="card-subtitle mb-2 text-muted">Crear nuevo iBrochure</h6>
            </div>
            <div class="float-end">
              <a href="/ventas/gacetadeenvio">Regresar</a>
            </div>
          </div>
          <hr>
            <form action="/gaceta/{{!$gaceta?"guardar":"actualizar"}}" method="post">
              @csrf
              @if ($gaceta)
                  <input type="hidden" name="id" value="{{$gaceta->id}}">
              @endif
              <div class="row">
                <div class="col-md-4">
                  <label data-toggle="tooltip" data-placement="top" title="El título no será enviado en el correo electrónico">
                    <i class="fa fa-info-circle"></i>
                    Título:
                  </label>
                  <input type="text" name="titulo" required placeholder="Nombre de la gaceta" class="form-control" value="{{$gaceta->titulo ?? ""}}">
                </div>
                <div class="col-md-4">
                  <label for="">Asunto:</label>
                  <input type="text" name="asunto" required placeholder="Asunto" class="form-control" value="{{$gaceta->asunto ?? ""}}">
                </div>
                <div class="col-md-4">
                  <label for="">Grupos:</label>
                  <select class="form-control" multiple data-role="tagsinput" id="grupos" name="grupos[]">
                    @foreach (json_decode($gaceta->grupos) as $item)
                        <option value="{{$item}}">{{$item}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                </br>
                <label for="">Contenido del iBrochure</label>
                  <textarea name="contenido" id="contenido1">{{$gaceta->contenido ?? ""}}</textarea>
                  <div class="clearfix">
                    <div class="float-end">
                    </br>
                      <button type="submit" class="btn btn-primary">
                        Guardar
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
    </div>

  </div>
@endsection
@section('styles')
  <style media="screen">
    .text-muted{
      color:#BD773E !important;
    }
    textarea{
      width:100%;
      height:400px;
    }
  </style>
@endsection
@section('scripts')
  <script src="https://cdn.tiny.cloud/1/4eh5se8bzh2rwh4i26sh1a582xzigey103wfcd1h7smr5czs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
  <script src="{{asset("/js/tinydo.js")}}?rand={{rand()}}"></script>
  <script>

  </script>
@endsection
