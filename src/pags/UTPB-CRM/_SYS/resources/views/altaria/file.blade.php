@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
 
 <p>Documento de ejemplo para adjuntar los numeros de telefono, Descargar <a href="{{asset('assets/docs/example.csv')}}"> Aqui</a>. </p>
<small>El formato de los numeros debe ser Codigo de pais seguido del numero nacional o internacional, Ej. 522461310790</small>
<hr>
<form action="{{route('altaria.filepost')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="formFileLg" class="form-label">Sube aquí tu archivo.</label>
        <input class="form-control form-control-lg" type="file" name="file" required>
    </div>
    <div class="col-12 form-group">
        <label for="msgText">Mensaje </label>
        <input type="text" name="msgText" id="msgText" class="form-control" required>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-success">Enviar</button>
    </div>
</form>
@endsection
@section('scripts')
@endsection