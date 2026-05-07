@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<center>
  <h2>
    Usuario suspendido
  </h2>
  <p>
    El usuario ha sido suspendido, para más información, contacte al Corporativo
  </p>
</center>
@endsection
