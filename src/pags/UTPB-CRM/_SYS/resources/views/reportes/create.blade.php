@extends('users.' . Auth::user()->level->alias . '.home')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="content mt-5">
    <form action="{{url('/reporte/make')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-row row">
            <div class="form-group col-md-12">
                <label for="name">Titulo de Reporte</label>
                <input type="text" class="form-control" id="titulo" placeholder="Titulo" required name="titulo">
            </div>
        </div>
        
        <div class="form-row row">
            <div class="form-group col-md-12">
                <label for="precio">Descripción detallada</label>
                <textarea class="form-control" id="descripcion" placeholder="Descripción" required  name="descripcion" rows="4"></textarea>
                {{-- <div id="editor"></div> --}}
            </div>
        </div>
        
        <div class="form-row row">
            <div class="form-group col-md-12">
                <label for="tipoDescuento">Area a donde va dirigido</label>
                @php
                    $areas = \App\level::get();
                @endphp
                <select class="form-control areaSelect" id="area" required  name="area[]" multiple="multiple">
                    <option value="all"0 data-lvl="all">Todas las Areas</option>
                    @foreach($areas as $area)
                    <option value="{{ md5($area->id) }}" data-lvl="{{ $area->id }}" id="opt{{ $area->id }}" <?php if($area->id == auth()->user()->levels->id){echo('selected');}?>>{{ $area->name }} 
                        @if($area->id == auth()->user()->levels->id)
                        (Mi área actual)
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row row">
            <div class="form-group col-md-12">
                <label for="tipoDescuento">Usuario en Especifico</label>
                <select class="form-control areaSelectUsers" id="users" required  name="users[]" multiple="multiple" disabled></select>
            </div>
        </div>

        <div class="form-row row">
            <div class="form-group col-md-12">
                <label for="tipoDescuento">Prioridad</label>
                <select class="form-control " id="tipoDescuento" required  name="prioridad">
                    <option value="2">Alta</option>
                    <option value="1">Media</option>
                    <option value="0">Baja</option>
                </select>
            </div>
        </div>

        {{-- <div class="form-row row"> --}}
            {{-- <div class="col-12" id="dropzone"> --}}
                {{-- <form action="/bandeja/upload" class="dropzone" id="dropzone"> --}}
                  {{-- <div class="fallback">
                    <input name="file" type="file" multiple />
                  </div> --}}
                {{-- </form> --}}
              {{-- </div> --}}
            {{-- <hr>
            <input type="file" multiple name="file[]" placeholder="Seleccione los documentos"> --}}
        {{-- </div> --}}
        {{-- <div class="form-row row">
            <div class="form-group col-md-12">
                <label for="formFile" class="form-label">¿Quieres Añadir un Archivo al Reporte?</label>
                <input class="form-control" type="file" id="archivo" name="file">
            </div>
        </div>
        <div class="form-row row">
            <div class="form-group col-md-12">
                <label for="fileTitle">Nombre del Archivo</label>
                <input type="text" class="form-control" id="fileTitle" placeholder="Nombre Archivo" name="fileTitle">
            </div>
        </div> --}}
        <hr>
        <small class="text-dark">Si quieres subir documentos, necesitas guardar primero el reporte.</small>
        <br>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
@section('scripts')
<script src="{{ asset('js/dropzone.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script>
    // 
    async function serverResponse(param={},url) {
      const result = await $.ajax({
        url: url,
        type: 'POST',
        data: param,
      })
      return result
    }

    $(document).ready(function() {
        $('.areaSelect').select2();
        $('.areaSelectUsers').select2();
        $('.areaSelect').change();
    });

    CKEDITOR.replace( 'descripcion' );
    // ClassicEditor
    // .create( document.querySelector( '#editor' ) )
    // .then( editor => {
    //         console.log( editor );
    // } )
    // .catch( error => {
    //         console.error( error );
    // } );

    $(".areaSelect").on( "change", async function(e) {
        var id = $(this).val();
        var usrcrnt = '{{ auth()->user()->id }}'
        var flagAll = 0,flagNone = 0;
        if(id!=null){
            id.some(async function(value){
                if(value=="all"){
                    flagAll = 1;
                    flagNone = 0;
                    $('.areaSelect').val("all");
                }
            });
            
            var users = await serverResponse({_token:'{{csrf_token()}}', id:id},'/reporte/getUserPerArea');
            var usersSlct='';
            var selected = '';
            usersSlct='<option value="all">Todos los usarios</option>';
            users.forEach(async function(element){
                if(parseInt(usrcrnt) == element.id){
                    selected = 'selected';
                }
                else{
                    selected = '';
                }
                usersSlct += `<option value="${element.id}" ${selected}>${element.name}</option>`;
            });
            $('#users').removeAttr('disabled');
            $('#users').html(usersSlct);
        }
        else{
            $('#users').attr('disabled','disabledz');
            $('#users').html('');
        }
    });

    $(".areaSelectUsers").on( "change", async function(e) {
        var usr = $(this).val();
        
        var flagAll = 0,flagNone = 0;
        usr.some(async function(value){
            if(value=="all"){
                flagAll = 1;
                flagNone = 0;
                $('.areaSelect').val("all");
            }
        });
    });
    
</script>
@endsection