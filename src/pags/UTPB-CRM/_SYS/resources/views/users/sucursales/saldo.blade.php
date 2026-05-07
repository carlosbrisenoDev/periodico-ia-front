@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h3 class="titulo">Saldo</h3>
          Se agregará el 5% del total de la compra al monedero electrónico. Esta acción será registrada en ventas e historial.
          <div class="col-12">
            <br>
              <form class="" action="/cart/addsaldo" method="post">
                <div class="col-12">
                  <div class="row">
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                      Código de usuario:
                      <input type="text" class="form-control" name="codigo" placeholder="S20192020">
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                      Consumo
                      <input type="text" class="form-control" name="consumo" placeholder="500">
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                      <br>
                      <input type="submit" class="btn btn-primary" name="" value="Añadir saldo">
                    </div>
                  </div>
                </div>
              </form>
          </div>
        </div>
      </div>
    </div>
 </div>
@endsection
