@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="modal" tabindex="-1" role="dialog" id="modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">SII</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>¿Desea eliminar {{$what}}?</p>
      </div>
      <div class="modal-footer">
        <a href="{{$noroot}}" class="btn btn-secondary">
        <i class="fa fa-cancel"></i>    No, regresar
      </a>
        <form class="form-horizontal" method="POST" action="{{$yesroot}}">
          <input type="hidden" name="id" value="{{$id}}">
              {{ csrf_field() }}
          <button type="submit" class="btn btn-primary">
          <i class="fa fa-trash"></i>    Si, eliminar
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(document).ready(function(){
      $("#modal").modal();
    });
  </script>
@endsection
