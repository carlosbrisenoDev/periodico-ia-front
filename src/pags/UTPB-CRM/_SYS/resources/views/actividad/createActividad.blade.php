@extends('users.'.Auth::user()->level->alias.'.home')
@section('styles')
<link rel="stylesheet" href="{{asset("/css/actividades.css")}}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<h2>Crear Actividad</h2>
<hr>
<div class="card">
    <form action="{{url('/actividadesCatalogo/register')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="card-body">
                <h6>Titulo de la Actividad</h6>
                <input type="text" name="titulo" required class="form-control">
                <hr>
                <h6>Tiempo promedio que toma en realizar la actividad <small>(En minutos)</small></h6>
                <input type="number" name="tiempo" required class="form-control">
                <hr>
                <h6>Enlista las tareas que tendra esta actividad, separadas por un salto de linea y sin indicador numerico ni simbolico.</h6>
                <textarea name="comment" style="width: 100%;white-space: pre-wrap;" class="form-control" style="white-space: pre-wrap;" id="list"></textarea>
                <hr>
                <div style="text-align: end;">
                    <button type="submit" class="btn btn-success btn-send">Registrar</button>
                </div>            
            </div>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script>
    // CKEDITOR.replace( 'comment' );
    $('.btn-send').click(function() {
        var lines = $('#list').val().split(/\n/);
        var output = [];
        var outputText = [];
        for (var i = 0; i < lines.length; i++) {
            // only push this line if it contains a non whitespace character.
            if (/\S/.test(lines[i])) {
            outputText.push('"' + $.trim(lines[i]) + '"');
            output.push($.trim(lines[i]));
            }
        }
        console.log(output);
        $('#list').val('[' + outputText + ']');
    })
</script>
@endsection