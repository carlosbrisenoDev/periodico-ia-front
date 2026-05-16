@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Finanzas</h6>
        <h6 class="card-subtitle mb-2 text-muted">Entradas/Salidas</h6>
        <hr>
        <form action="/finanzas/guardar" method="post">
          <div class="row">
            <div class="col-3">
              <label for="">Concepto:</label>
              <input type="text" name="concepto" placeholder="Concepto" class="form-control" value="">
            </div>
            <div class="col-3">
              <label for="">Tipo:</label>
              <select class="form-control" name="tipo">
                <option value="Ingreso">Ingreso</option>
                <option {{isset($_REQUEST["salida"]) ? "selected" : ""}} value="Salida">Salida</option>
              </select>
            </div>
            <div class="col-3">
              <label for="">Monto de operación:</label>
              <input type="number"  name="monto" class="form-control" placeholder="0.00" value="">
            </div>
            <div class="col-3">
              <br>
              <input type="submit" class="btn btn-info large" name="" value="Guardar">
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12 col-md-12 col-lg-6 table-responsive">
              <h6 class="card-subtitle mb-2 text-muted">Entradas</h6>
              <table class="table">
                <thead class="bg-dark">
                  <tr>
                    <th>Folio</th>
                    <th>Usuario</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th></th>
                  </tr>
                </thead>
                @php
                  $total = 0;
                @endphp
                @foreach (\App\finanzas::where("tipo","Ingreso")->get() as $key)
                  <tr>
                    <td>{{$key->id}}</td>
                    <td>{{$key->user->name}}</td>
                    <td>{{$key->concepto}}</td>
                    <td>${{number_format($key->monto,2,".",",")}}</td>
                    <td>
                      <a href="/finanzas/eliminar/do?cid={{md5($key->id)}}">
                        <i class="fa fa-trash text-danger"></i>
                      </a>
                    </td>
                    @php
                      $total += $key->monto;
                    @endphp
                  </tr>
                @endforeach
                <tfoot class="bg-success text-light">
                  <td colspan="2">
                    Total ingresos
                  </td>
                  <td>
                    ${{number_format($total,2,".",",")}}
                  </td>
                  <td></td>
                  <td></td>
                </tfoot>
              </table>
            </div>
            <div class="col-12 col-md-12 col-lg-6">
              <h6 class="card-subtitle mb-2 text-muted">Salidas</h6>
              <table class="table table-striped">
                <thead class="bg-dark">
                  <tr>
                    <th>Folio</th>
                    <th>Usuario</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th></th>
                  </tr>
                </thead>
                @php
                  $total_2 = 0;
                @endphp
                @foreach (\App\finanzas::where("tipo","Salida")->get() as $key)
                  <tr>
                    <td>{{$key->id}}</td>
                    <td>{{$key->user->name}}</td>
                    <td>{{$key->concepto}}</td>
                    <td>${{number_format($key->monto,2,".",",")}}</td>
                    <td>
                      <a href="/finanzas/eliminar/do?cid={{md5($key->id)}}">
                        <i class="fa fa-trash text-danger"></i>
                      </a>
                    </td>
                    @php
                      $total_2 += $key->monto;
                    @endphp
                  </tr>
                @endforeach
                <tfoot class="bg-danger text-light">
                  <td colspan="2">
                    Total egresos
                  </td>
                  <td>
                    ${{number_format($total_2,2,".",",")}}
                  </td>
                  <td></td>
                  <td></td>
                </tfoot>
              </table>
            </div>
            <div class="col-12">
              <div class="row">
                <div class="col"></div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                  <table class="table table-striped">
                    <tr>
                      @php
                        $status = $total-$total_2;
                      @endphp
                      <td>Estado financiero:</td>
                      <td class="text-{{($status > 0 ?"success" : "danger")}}">
                        ${{number_format($status,2,".",",")}}
                      </td>
                    </tr>
                  </table>
                  <center>
                    @if ($status == 0)
                      <i class="fas fa-meh fa-3x"></i>
                    @endif
                    @if ($status < 0)
                      <i class="fas fa-sad-cry text-danger fa-3x"></i>
                    @endif
                    @if ($status > 0)
                      <i class="fas fa-smile-beam text-success fa-3x"></i>
                    @endif
                  </center>
                </div>
                <div class="col"></div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
