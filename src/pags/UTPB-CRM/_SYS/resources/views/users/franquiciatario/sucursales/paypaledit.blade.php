@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                    <div class="row">
                      <div class="col-12">
                        <div class="clearfix">
                          <div class="pull-left">
                            <h3>Mis cuentas</h3>
                          </div>
                          <div class="pull-right">

                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <form class="form-horizontal" method="POST" autocomplete="off" action="/paypals/refresh">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Alias de la cuenta</label>
                            <input type="text" autofocus name="alias" value="{{$p->alias}}" required class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Alias">
                          </div>
                          <input type="hidden" name="id" value="{{$p->id}}">
                          <div class="form-group">
                            <label for="exampleInputEmail2">Username</label>
                            <input type="text" autofocus name="username" value="{{$p->username}}" required class="form-control" id="exampleInputEmail2" aria-describedby="emailHelp" placeholder="_api.gruposhirushi.com">
                          </div>
                          <div class="form-group">
                            <label for="exampleInputEmail3">Password</label>
                            <input type="text" autofocus name="password" value="{{$p->password}}" required class="form-control" id="exampleInputEmail3" aria-describedby="emailHelp" placeholder="78asd8ASDAS7878">
                          </div>
                          <div class="form-group">
                            <label for="exampleInputEmail4">Secret</label>
                            <input type="text" autofocus name="secret" required value="{{$p->secret}}" class="form-control" id="exampleInputEmail4" aria-describedby="emailHelp" placeholder="JaksdjIUajksjw9291JaksdjIUajksjw9291JaksdjIUajksjw9291JaksdjIUajksjw9291">
                          </div>
                          <button type="submit" class="send hidden"></button>
                        </form>
                      </div>
                      <div class="col">

                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <button type="button" onclick="$('.send').click();" class="btn btn-primary">Guardar</button>
                        <a href="/paypals/delete/{{$p->id}}" class="btn btn-default" data-dismiss="modal">Eliminar</a>
                      </div>
                      </div>
                    </div>


                </div>
            </div>
        </div>
@endsection

@section('scripts')
  <script type="text/javascript">
    $(".new").bind("click",function(){
      $(".modal").modal();
    });
  </script>
@endsection
