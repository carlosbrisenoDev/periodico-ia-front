@extends('layouts.website')
@section('content')
  <div class="row">
    <div class="col">
      <div class="text–center">
        <div class="card" style="text-align: justify;width:50%;padding:20px;margin-left:auto;margin-right:auto;">
          <h2 class="text-center">Mensaje</h2>
          <hr>
          {{ session('texto') }}
      </div>
    </div>
  </div>
</div>
@endsection
