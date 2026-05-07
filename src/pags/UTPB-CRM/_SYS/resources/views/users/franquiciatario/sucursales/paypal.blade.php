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
                            <a href="#" class="btn btn-primary new">
                              <i class="fa fa-plus"></i> Agregar cuenta Paypal
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <div class="row">
                      <div class="col">
                        <p>
                          <p align="justify">
                            Crea una cuenta de Paypal empresarial para recibir los depositos de las compras en linea, una vez creada, agregala usando el botón "Nueva cuenta paypal", selecciona la cuenta por defecto a usar para todas tus sucursales.
                          </p>
                          <ul>
                            <li>1. Crea una cuenta de Paypal Empresarial (Recuerda poner el nombre de la sucursal de Shirushi en el nombre de la Empresa al registrar).
                            </li><li>2. Configura tu cuenta (Confirma, Agrega una cuenta de deposito, etc).
                            </li><li>3. Accede a <a href="https://www.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature">Accesos API</a> para obtener tu información de cuenta.
                            </li><li>4. Agrega una nueva cuenta.
                            </li><li>5. Selecciona la cuenta principal de deposito.
                            </li><li>6. Haz click en Establecer cuenta principal.</li>
                          </ul>


                          <p>
                            <i>Sí no seleccionas una cuenta por defecto, se usará la primer cuenta registrada en esta cuenta</i>
                          </p>
                          <p>
                            <i>Sí no registras una cuenta, tu sucursal no recibirá pedidos a domicilio.</i>
                          </p>
                        </p>
                      </div>
                    </div>
                    <hr>
                    <form class="" action="/paypals/default" method="post">
                      <div class="row">
                          @if (count(Auth::user()->paypals) > 0)
                            @foreach (Auth::user()->paypals as $paypal)
                              <div class="col-2 text-center">
                                <a class="no-decoration primary" href="/paypals/edit/{{md5($paypal->id)}}">
                                  <div class="card border-light text-center">
                                    <i class="fas fa-key fa-2x"></i>
                                    <div class="card-body">
                                    </div>
                                  </div>
                                </a>
                                <input type="radio" autocomplete="off" {{(Auth::user()->defecto == $paypal->id) ? "checked" : ""}} name="cuenta_id" id="selec{{$paypal->id}}" value="{{$paypal->id}}">
                              </br>
                                <label for="selec{{$paypal->id}}">{{$paypal->alias}}</label>
                              </div>
                            @endforeach
                          @endif
                        </div>
                        <hr>
                        <hr>
                        <div class="row">
                          <div class="col">
                            <button type="submit" class="btn btn-primary" name="button">Establecer cuenta por defecto</button>
                          </div>
                        </div>
                  </form>
                </div>
            </div>
        </div>
@endsection
@section('pop')
  <div class="modal fade" id="data" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Nueva cuenta de Paypal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" autocomplete="off" action="/paypals/guardar">
            <div class="form-group">
              <label for="exampleInputEmail1">Alias de la cuenta</label>
              <input type="text" autofocus name="alias" required class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Alias">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail2">Username</label>
              <input type="text" autofocus name="username" required class="form-control" id="exampleInputEmail2" aria-describedby="emailHelp" placeholder="_api.gruposhirushi.com">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail3">Password</label>
              <input type="text" autofocus name="password" required class="form-control" id="exampleInputEmail3" aria-describedby="emailHelp" placeholder="78asd8ASDAS7878">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail4">Secret</label>
              <input type="text" autofocus name="secret" required class="form-control" id="exampleInputEmail4" aria-describedby="emailHelp" placeholder="JaksdjIUajksjw9291JaksdjIUajksjw9291JaksdjIUajksjw9291JaksdjIUajksjw9291">
            </div>
            <button type="submit" class="send hidden"></button>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" onclick="$('.send').click();" class="btn btn-primary">Guardar</button>
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
