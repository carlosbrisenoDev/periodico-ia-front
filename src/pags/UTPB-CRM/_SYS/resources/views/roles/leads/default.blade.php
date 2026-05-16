@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Analizador de Leads</h5>
        <h6 class="card-subtitle mb-2 text-muted"></h6>
        <hr>
        @php
          $desde = Request::has("desde") ? Request::get("desde") : Date("Y-m-")."01";
          $hasta = Request::has("hasta") ? Request::get("hasta") : Date("Y-m-d");
          $inscritos = Request::has("inscritos") ? Request::get("inscritos") : 0;
          $ladas = Request::has("ladas") ? Request::get("ladas") : [];
        @endphp
        <form class="" action="" method="get">
          <div class="row">
            <div class="col-12 col-md-6 col-lg-4 col-xl-4">
              <label for="">Desde fecha</label>
              <input type="date" class="form-control" name="desde" value="{{$desde}}">
              <label for="">Hasta fecha</label>
              <input type="date" class="form-control" name="hasta" value="{{$hasta}}">
            </div>
            <div class="col-12 col-md-6 col-lg-4 col-xl-4">
              <label for="">Inscritos</label>
              <select class="form-control" name="inscritos">
                <option {{$inscritos == 0 ? "selected" : ""}} value="0">Todos</option>
                <option {{$inscritos == 1 ? "selected" : ""}} value="1">Inscritos</option>
                <option {{$inscritos == 2 ? "selected" : ""}} value="2">No inscritos</option>
              </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4 col-xl-4">
              <label for="">Lada</label>
              <select class="form-control" name="ladas[]" style="height:400px;" multiple>
                @foreach (\App\ladas::all() as $lada)
                  <option {{in_array($lada->lada,$ladas) ? "selected" : ""}} value="{{$lada->lada}}">{{$lada->lada}} - {{$lada->lugar}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <hr>
              <div class="clearfix">
                <div class="float-end">
                  <button type="submit" class="btn btn-primary btn-sm" name="button">Buscar</button>
                </div>
              </div>
            </div>
          </div>
        </form>
        <hr>
        @php
        $ladac = "";
        if(count($ladas) > 0){
          $newladas = "";
          foreach($ladas as $lada){
            $newladas .= "^$lada([0-9]+){7}$|";
          }
          $newladas = substr($newladas,0,-1);
          $ladac = "and telefono regexp '$newladas'";
        }
        $clientes = \App\cliente::whereRAW("date(created_at) >= '$desde' and date(created_at) <= '$hasta' $ladac");
        if($inscritos == 1){
          $clientes->whereHas("get_comprobante");
        }
        $clientes->selectRAW('*, replace(telefono,SUBSTRING(telefono,-7),"") as ladax, (select lugar from ladas as l where l.lada = SUBSTR(ladax,1,LENGTH(lada)) limit 1) as lugar, (select lada from ladas as l where l.lada = SUBSTR(ladax,1,LENGTH(lada)) limit 1) as reallada');
        @endphp
        <br>
        <button id="btnExport" class="btn btn-primary btn-sm" onclick="exportReportToExcel(this)">Descargar como Excel</button>
        <br>
        <br>
        <table class="table table-striped">
          <thead class="bg-dark">
            <th>Nombre</th>
            <th>Paterno</th>
            <th>Materno</th>
            <th>Correo</th>
            <th>Lada</th>
            <th>Teléfono</th>
            <th>Alta</th>
            <th>Inscrito</th>
            <th>Lugar</th>
            <th>Vendedor</th>
          </thead>
          <tbody>
            @foreach ($clientes->get() as $cliente)
              <tr>
                <td>
                  {{mb_strtoupper($cliente->nombre)}}
                </td>
                <td>
                  {{mb_strtoupper($cliente->apat)}}
                </td>
                <td>
                  {{mb_strtoupper($cliente->amat)}}
                </td>
                <td>
                  {{$cliente->correo ?? "Sin correo electrónico"}}
                </td>
                <td>
                  {{$cliente->reallada ?? "Sin teléfono"}}
                </td>
                <td>
                  {{$cliente->telefono ?? "Sin teléfono"}}
                </td>
                <td>
                  {{\Carbon\carbon::parse($cliente->created_at)->format("Y-m-d")}}
                </td>
                <td>
                  {{$cliente->comprobante ? "Inscrito" : "No inscrito"}}
                </td>
                <td>
                  {{$cliente->lugar ?? "Sin lugar"}}
                </td>
                <td>
                  {{$cliente->agente->name ?? "Sin lugar"}}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
  <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
  <script type="text/javascript">
    function exportReportToExcel() {
      let table = document.getElementsByTagName("table"); // you can use document.getElementById('tableId') as well by providing id to the table tag
      TableToExcel.convert(table[0], { // html code may contain multiple tables so here we are refering to 1st table tag
        name: `export.xlsx`, // fileName you could use any name
        sheet: {
          name: 'Sheet 1' // sheetName
        }
      });
      }
  </script>
@endsection
