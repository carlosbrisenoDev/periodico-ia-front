@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  {{-- PHP DE PRODUCTO GENERAL --}}
  @php
    $nameSegunda = '';
    $tipoCursamiento = '';
    $materiaName = '';
    $showMateria = 'none';
    $show = '';
    if(auth()->user()->levels->alias=='administrador'){
      $show = '';
    }
    elseif(auth()->user()->levels->alias=='ventas'){
      $show = 'd-none';
    }
    $calculadora_data = json_decode($registro_data->data_calc_json);

    $empresa = $calculadora_data->empresa;
    $producto = $calculadora_data->producto;
    $descuento =  $calculadora_data->descuento;
    $pasarela =  $calculadora_data->pasarela;
    $descuentoAdicional =  $calculadora_data->descuento_adicional;
    // dd($producto);
    $descuentoLabel = 0;
    $descuentoCantidad = 0;
    $descuentoTipo = '%';
    if($descuento){
      $descuentoCantidad = $descuento->cantidad;
      $descuentoTipo = $descuento->tipo;
      if($descuento->tipo=='$'){
        $descuentoLabel = $descuento->cantidad/100;
      }
      elseif($descuento->tipo=='%'){
        $descuentoLabel = $producto->precio*($descuento->cantidad/100);
      }  
    } 
      
    
    $precioStandar = $producto->precio;
    $precioCnDescuento = $producto->precio-$descuentoLabel;
    
    
    if($pasarela){
      $comisionPasarelaFija = $pasarela->comision_fija;
      $comisionPasarela = $pasarela->comision;
      $cargoPasarela = ((($pasarela->comision/100)*$precioCnDescuento)+$pasarela->comision_fija);
    }
    else{
      $cargoPasarela = 0;
      $comisionPasarela = 0;
      $comisionPasarelaFija = 0;
    }

    $beneficio = $precioCnDescuento-$cargoPasarela-$producto->costo;
    // dd($producto->costo);
    $descuentoAdicionalSelect = ['1' => '<option value="1">Porcentaje</option>' , '2' => '<option value="2">Precio Fijo</option>'];

    if($descuentoAdicional->tipo_descuento==1){
      $descuentoAdicionalLabel = ($beneficio*($descuentoAdicional->descuento_adicional/100));
    }
    elseif($descuentoAdicional->tipo_descuento==2){
      $descuentoAdicionalLabel = $descuentoAdicional->descuento_adicional;
    }  

    $precioCnDescuentoAdicional = $beneficio-$descuentoAdicionalLabel+$producto->costo;
    if($descuentoAdicional->descuento_adicional>0){
      $comisionLabel = ($precioCnDescuentoAdicional)*((($producto->comision/$precioStandar)*100)/100);
    }
    else{
      $comisionLabel = $producto->comision;
    }

    $utilidadLabel = $precioCnDescuentoAdicional-$comisionLabel;
    if($utilidadLabel<500){
      $utilidadLabel = 500;
      $comisionLabel = $precioCnDescuentoAdicional-$utilidadLabel;
    }
  @endphp
  {{-- PHP DE PRODUCTO GENERAL --}}

  @if($calculadora_data->curso)
    @php
    
      if($calculadora_data->curso){
        if($calculadora_data->curso=='colegiatura'){
          $materia = $producto;
          $precioStandar_materia = $materia->costo_mensualidad;
          $costo_materia = $materia->costo_materia;
          $varName = 'mensualidad';
          $nameSegunda  = 'Mensualidad';
          $tipoCursamiento = 'Materia';
          $materiaName = 'No data';
          $showMateria = 'none';
        }
        if($calculadora_data->curso=='materia'){
          $materia = $producto;
          $precioStandar_materia = $materia->precio_mensualidad;
          $costo_materia = $materia->costo_mensualidad;
          $varName = 'materia';
          $nameSegunda  = 'Materia';
          $tipoCursamiento = 'Materia';
          // $materiaName = $materia->asignatura;
          $showMateria = 'block';
        }
      }
      
      // dd($materia);
      if($materia){
        $descuento_materia =  $calculadora_data->descuento_materia;
        $pasarela_materia =  $calculadora_data->pasarela_materia;
        $descuentoAdicional_materia =  $calculadora_data->descuento_adicional_materia;
        $descuentoAdicionalInput_materia = $descuentoAdicional_materia->descuento_adicional;
        
        $descuentoLabel_materia = 0;
        $descuentoCantidad_materia = 0;
        $descuentoTipo_materia = '%';
        
        if($descuento_materia){
          $descuentoCantidad_materia = $descuento_materia->cantidad;
          $descuentoTipo_materia = $descuento_materia->tipo;
          if($descuento_materia->tipo=='$'){
            $descuentoLabel_materia = $descuento_materia->cantidad/100;
          }
          elseif($descuento_materia->tipo=='%'){
            $descuentoLabel_materia = $precioStandar_materia*($descuento_materia->cantidad/100);
          }  
        } 
        
        $precioCnDescuento_materia = $precioStandar_materia-$descuentoLabel_materia;
        
        if($pasarela_materia){
          $pasarelaName_materia = $pasarela_materia->nombre;
          $comisionPasarelaFija_materia = $pasarela_materia->comision_fija;
          $comisionPasarela_materia = $pasarela_materia->comision;
          $cargoPasarela_materia = ((($pasarela_materia->comision/100)*$precioCnDescuento_materia)+$pasarela_materia->comision_fija);
        }
        else{
          $cargoPasarela_materia = 0;
          $comisionPasarela_materia = 0;
          $comisionPasarelaFija_materia = 0;
        }
        if($calculadora_data->curso=='colegiatura'){
          $beneficio_materia = $precioCnDescuento_materia-$cargoPasarela_materia-$materia->costo_mensualidad;
        }
        
        if($calculadora_data->curso=='materia'){
          $beneficio_materia = $precioCnDescuento_materia-$cargoPasarela_materia-$materia->costo_materia;
        }
        $descuentoAdicionalSelect_materia = ['1' => '<option value="1">Porcentaje</option>' , '2' => '<option value="2">Precio Fijo</option>'];

        if($descuentoAdicional_materia->tipo_descuento==1){
          $descuentoAdicionalLabel_materia = ($beneficio_materia*($descuentoAdicional_materia->descuento_adicional/100));
        }
        elseif($descuentoAdicional_materia->tipo_descuento==2){
          $descuentoAdicionalLabel_materia = $descuentoAdicional_materia->descuento_adicional;
        }  

        $precioCnDescuentoAdicional_materia = $beneficio_materia-$descuentoAdicionalLabel_materia;
        if($descuentoAdicional_materia->descuento_adicional>0){
          if($calculadora_data->curso=='colegiatura'){
            $comisionLabel_materia = ($precioCnDescuentoAdicional_materia)*((($materia->comision_mensualidad/$precioStandar_materia)*100)/100);
          }
          
          if($calculadora_data->curso=='materia'){
            $comisionLabel_materia = ($precioCnDescuentoAdicional_materia)*((($materia->comision_materia/$precioStandar_materia)*100)/100);
          }
          
        }
        else{
          if($calculadora_data->curso=='colegiatura'){
            $comisionLabel_materia = $materia->comision_mensualidad;
          }
          
          if($calculadora_data->curso=='materia'){
            $comisionLabel_materia = $materia->comision_materia;
          }
          
        }

        $utilidadLabel_materia = $precioCnDescuentoAdicional_materia-$comisionLabel;
        if($utilidadLabel_materia<500){
          $utilidadLabel_materia = 500;
          $comisionLabel_materia = $precioCnDescuentoAdicional_materia-$utilidadLabel;
        }
      }
      else{
        $precioStandar_materia = 0;
        $descuento_materia =  0;
        $pasarela_materia =  0;
        $descuentoAdicional =  0;
        $descuentoAdicionalInput_materia = 0;
        $pasarelaName_materia = 'Sin pasarela.';
        // dd($producto);
        $descuentoLabel_materia = 0;
        $descuentoCantidad_materia = 0;
        $descuentoTipo_materia = '%';
        $costo_materia = 0;
        
        $precioCnDescuento_materia = 0;
        $cargoPasarela_materia = 0;
        $comisionPasarela_materia = 0;
        $comisionPasarelaFija_materia = 0;

        $beneficio_materia = 0;
        $descuentoAdicionalSelect_materia = ['1' => '<option value="1">Porcentaje</option>' , '2' => '<option value="2">Precio Fijo</option>'];
        $descuentoAdicionalLabel_materia = 0;
        $comisionLabel_materia = 0;       
        $precioCnDescuentoAdicional_materia = 0; 
        
        $utilidadLabel_materia = 0;
        
      }
      
    @endphp
  @endif
  
  <div class="card">
    <div class="card-body">
      <button class="btn btn-info" id="report-generate">Imprimir / Generar Reporte</button>
      <a class="btn btn-warning" href="{{route('calculadora.misDatos')}}">Ver mis calculos/tickets</a>
      @if(auth()->user()->levels->alias=='administrador')
      <a class="btn btn-danger" href="{{route('calculadora.datosGenerales')}}">Ver calculos/tickets Generados por otros usuarios</a>
      @endif
    </div>
  </div>

  <div class="content mt-5">
    <div class="row">
        <div class="col">
            <div class="card">
              <div class="card-header">
                  Calculadora 1
                  <br>
                  {{-- <small>Si quieres alterar los datos dummy de la calculadora el archivo esta en: /js/calculadora_data.js</small> --}}
              </div>
              <div class="card-body">
                    <form>
    
                        <div class="form-group col-12 mb-4">
                            <label for="empresa">Empresa</label>
                            <select class="form-control form-control-sm" id="empresa" disabled>
                                <option selected>{{$empresa->nombre}}</option>
                            </select>
                        </div>
                        
                        <div class="form-group col-12 mb-4">
                            <label for="producto">Producto</label>
                            <select class="form-control form-control-sm" id="producto" disabled>
                                <option selected>{{$producto->nombre}}</option>
                            </select>
                        </div>
    
                        <div class="form-group row mb-4">
                            <label for="precioEstandar" class="col-sm-5 col-form-label">Precio Estandar</label>
                            <div class="col-sm-6">
                              <input type="text" class="form-control" id="precioEstandar" placeholder="00.00" disabled value="$ {{$producto->precio}}">
                            </div>
                        </div>
    
                        <div class="form-group row mb-4">
                          <label for="descuento" class="col-sm-5 col-form-label">Agregar Descuento</label>
                          <div class="col-sm-2">
                            <input type="text" class="form-control" id="descuento" placeholder="00 %" disabled value="{{$descuentoCantidad}}">
                          </div>
                          <label for="descuento" class="col-sm-4 col-form-label">Maximo descuento: <span id="maxDescuento">{{$descuentoTipo}}</span></label>
                        </div>
    
                        <div class="form-group row mb-4">
                            <label for="descuentoField" class="col-sm-5 col-form-label">Descuento</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="descuentoField" placeholder="00.00" disabled value="$ {{ $descuentoLabel }}">
                            </div>
                        </div>
    
                        <div class="form-group row mb-4 {{$show}}">
                            <label for="costo" class="col-sm-5 col-form-label">Costo</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="costo" placeholder="00.00" disabled value="$ {{$producto->costo}}">
                            </div>
                        </div>

                        <div class="form-group row mb-4 ">
    
                            <label for="pasarelaComisionFija" class="col-sm-5 col-form-label">Pasarela Elegida</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="pasarelaComisionFija" value="{{$pasarela->nombre ?? 'Sin Pasarela' }}" disabled>
                            </div>
                        </div>
    
                        <div class="form-group row mb-4 {{$show}}">
    
                            <label for="pasarelaComisionFija" class="col-sm-5 col-form-label">Pasarela Fija</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="pasarelaComisionFija" value="$ {{ $comisionPasarelaFija ?? '00.00' }}" disabled>
                            </div>
                        </div>
    
                        <div class="form-group row mb-4 {{$show}}">
                          <label for="pasarelaComision" class="col-sm-5 col-form-label">Pasarela Comisión</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="pasarelaComision" placeholder="00.00" disabled value="$ {{((($comisionPasarela/100)*$precioCnDescuento)) ?? '00.00' }}">
                          </div>
    
                          {{-- <label for="pasarelaComisionFija" class="col-sm-3 col-form-label">Pasarela Fija</label>
                          <div class="col-sm-2">
                            <input type="text" class="form-control" id="pasarelaComisionFija" placeholder="00.00" disabled>
                          </div> --}}
                      </div>
    
                        <div class="form-group row mb-4">
                          <label for="pasarelaComisionTotal" class="col-sm-5 col-form-label">Pasarela Cargos</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="pasarelaComisionTotal" placeholder="00.00" disabled value="$ {{($cargoPasarela) ?? '00.00' }}">
                          </div>
                        </div>
    
                        <div class="form-group row mb-4 {{$show}}">
                            <label for="beneficio" class="col-sm-5 col-form-label">Beneficio</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="beneficio" placeholder="00.00" disabled value="$ {{$beneficio}}">
                            </div>
                        </div>
    
                        <div class="form-group row mb-4">
                            <label for="tipoDescuento" class="col-sm-5 col-form-label">Tipo Descuento</label>
                            <div class="col-sm-5">
                                <select class="form-control form-control-sm" id="tipoDescuento" disabled>
                                  {!! $descuentoAdicionalSelect[$descuentoAdicional->tipo_descuento] !!}
                                    
                                    
                                </select>
                            </div>
                        </div>
    
                        <div class="form-group row mb-4">
                          <label for="descuentoAdicional" class="col-sm-5 col-form-label">Descuento Adicional</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="descuentoAdicional" placeholder="00.00" disabled value="{{$descuentoAdicional->descuento_adicional}}">
                          </div>
                      </div>
    
                        <div class="form-group row mb-4">
                            <label for="descuentoAdicionalField" class="col-sm-5 col-form-label">Descuento</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="descuentoAdicionalField" placeholder="00.00" disabled value="$ {{$descuentoAdicionalLabel}}">
                            </div>
                        </div>
    
                        <div class="form-group row mb-4 {{$show}}">
                            <label for="beneficioFinal" class="col-sm-5 col-form-label">Beneficio Final</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="beneficioFinal" placeholder="00.00" disabled style="background-color: rgba(255,205,0,0.3);" value="$ {{$precioCnDescuentoAdicional}}">
                            </div>
                        </div>
    
                        <div class="form-group row mb-4">
                            <label for="comision" class="col-sm-5 col-form-label">Comisión</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="comision" placeholder="00.00" disabled value="$ {{ $comisionLabel ?? 0}}">
                            </div>
                        </div>
    
                        <div class="form-group row mb-4 {{$show}}">
                          <label for="utilidad" class="col-sm-5 col-form-label">Utilidad</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="utilidad" placeholder="00.00" disabled value="$ {{$utilidadLabel}}">
                          </div>
                      </div>
    
                        <div class="form-group row mb-4">
                          <label for="nuevoPrecio" class="col-sm-5 col-form-label">Precio Final Para Comprador</label>
                          <div class="col-sm-12">
                            <input type="text" class="form-control" id="nuevoPrecio" placeholder="00.00" disabled style="background-color: rgba(0,127,0,0.4);" value="$ {{$precioCnDescuentoAdicional}}">
                          </div>
                        </div>
                    </form>
              </div>
          </div>
        </div>
        
        <div class="col">
          <div class="card">
                <div class="card-header">
                    Calculadora 2
                    <br>
                    {{-- <small>Si quieres alterar los datos dummy de la calculadora el archivo esta en: /js/calculadora_data.js</small> --}}
                </div>
                
                @if($calculadora_data->curso)
                <div class="card-body">
                  <form>

                    <div class="form-group col-12 mb-4">
                        <label for="course">Tipo de Cursamiento</label>
                        <select class="form-control form-control-sm" id="course" disabled>
                            <option>{{$tipoCursamiento}}</option>
                        </select>
                    </div>
                    <div id="info-course">

                      <div class="form-group row mb-4">
                          <label for="precioEstandar_materia" class="col-sm-5 col-form-label" id="precioEstandar_materiaLabel">Precio Estandar de {{$nameSegunda}}</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="precioEstandar_materia" placeholder="00.00" disabled value="$ {{$precioStandar_materia}}">
                          </div>
                      </div>

                      <div class="form-group row mb-4">
                        <label for="descuento_materia" class="col-sm-5 col-form-label" id="descuento_materiaLabel">Agregar Descuento de {{$nameSegunda}}</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control" id="descuento_materia" placeholder="00 %" disabled value="{{$descuentoCantidad_materia}}">
                        </div>
                        <label for="maxDescuento_materia" class="col-sm-4 col-form-label">Maximo descuento: <span id="maxDescuento_materia">N/A</span></label>
                      </div>

                      <div class="form-group row mb-4">
                          <label for="descuentoField_materia" class="col-sm-5 col-form-label" id="descuentoField_materiaLabel">Descuento de {{$nameSegunda}}</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="descuentoField_materia" placeholder="00.00" disabled value="$ {{$descuentoLabel_materia}}">
                          </div>
                      </div>

                      <div class="form-group row mb-4 {{$show}}">
                          <label for="costo_materia" class="col-sm-5 col-form-label" id="costo_materiaLabel">Costo de {{$nameSegunda}}</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="costo_materia" placeholder="00.00" disabled value="$ {{$costo_materia}}">
                          </div>
                      </div>

                      <div class="form-group row mb-4 ">
    
                          <label for="pasarelaComisionFija" class="col-sm-5 col-form-label">Pasarela Elegida</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="pasarelaComisionFija" value="{{$pasarelaName_materia ?? 'Sin Pasarela' }}" disabled>
                          </div>
                      </div>
                      
                      <div class="form-group row mb-4 {{$show}}">

                          <label for="pasarelaComisionFija_materia" class="col-sm-5 col-form-label" id="pasarelaComisionFija_materiaLabel">Pasarela Fija de {{$nameSegunda}}</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="pasarelaComisionFija_materia" placeholder="00.00" disabled value=" $ {{$comisionPasarelaFija_materia}}">
                          </div>
                      </div>

                      <div class="form-group row mb-4 {{$show}}">
                        <label for="pasarelaComision_materia" class="col-sm-5 col-form-label" id="pasarelaComision_materiaLabel">Pasarela Comisión de {{$nameSegunda}}</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="pasarelaComision_materia" placeholder="00.00" disabled value="$ {{$comisionPasarela_materia}}">
                        </div>
                      </div>

                      <div class="form-group row mb-3">
                        <label for="pasarelaComisionTotal_materia" class="col-sm-5 col-form-label" id="pasarelaComisionTotal_materiaLabel">Pasarela Cargos de {{$nameSegunda}}</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="pasarelaComisionTotal_materia" placeholder="00.00" disabled value="$ {{$cargoPasarela_materia}}">
                        </div>
                      </div>

                      
                      <div class="form-group row mb-4 {{$show}}">
                          <label for="beneficio_materia" class="col-sm-5 col-form-label" id="beneficio_materiaLabel">Beneficio de {{$nameSegunda}}</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="beneficio_materia" placeholder="00.00" disabled value="$ {{$beneficio_materia}}">
                          </div>
                      </div>

                      <div class="form-group row mb-4">
                        <label for="descuentoAdicional_materia" class="col-sm-5 col-form-label" id="descuentoAdicional_materiaLabel">Descuento Adicional de {{$nameSegunda}}</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="descuentoAdicional_materia" placeholder="00.00" disabled value="{{$descuentoAdicionalInput_materia}}">
                        </div>
                      </div>

                      <div class="form-group row mb-4">
                          <label for="tipoDescuento_materia" class="col-sm-5 col-form-label" id="tipoDescuento_materiaLabel">Tipo Descuento de {{$nameSegunda}}</label>
                          <div class="col-sm-5">
                              <select class="form-control form-control-sm" id="tipoDescuento_materia" disabled>
                                {!! $descuentoAdicionalSelect_materia[$descuentoAdicional_materia->tipo_descuento] !!}
                              </select>
                          </div>
                      </div>

                      <div class="form-group row mb-4">
                          <label for="descuentoAdicionalField_materia" class="col-sm-5 col-form-label" id="descuentoAdicionalField_materiaLabel">Descuento de {{$nameSegunda}}</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="descuentoAdicionalField_materia" placeholder="00.00" disabled value="$ {{$descuentoAdicionalLabel_materia}}">
                          </div>
                      </div>

                      <div class="form-group row mb-4 {{$show}}">
                          <label for="beneficioFinal_materia" class="col-sm-5 col-form-label" id="beneficioFinal_materiaLabel">Beneficio Final de {{$nameSegunda}}</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="beneficioFinal_materia" placeholder="00.00" disabled style="background-color: rgba(255,205,0,0.3);" value="$ {{$precioCnDescuentoAdicional}}">
                          </div>
                      </div>

                      <div class="form-group row mb-4" {{$show}}>
                          <label for="comision_materia" class="col-sm-5 col-form-label" id="comision_materiaLabel">Comisión <span id="comision-bussiness"></span></label>
                          <div class="col-sm-5">
                              <input type="text" class="form-control" id="comision_materia" placeholder="00.00" disabled value="$ {{$comisionLabel_materia}}">
                          </div>
                      </div>

                      <div class="form-group row mb-4" {{$show}}>
                        <label for="utilidad_materia" class="col-sm-5 col-form-label" id="utilidad_materiaLabel">Utilidad de {{$nameSegunda}}</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="utilidad_materia" placeholder="00.00" disabled value="$ {{$utilidadLabel_materia}}">
                        </div>
                      </div>

                      <div class="form-group row mb-4">
                        <label for="nuevoPrecio_materia" class="col-sm-12 col-form-label" id="nuevoPrecio_materiaLabel">Precio Final Para Comprador de {{$nameSegunda}}</label>
                        <div class="col-sm-12">
                          <input type="text" class="form-control" id="nuevoPrecio_materia" placeholder="00.00" disabled style="background-color: rgba(0,127,0,0.4);" value="$ {{$precioCnDescuentoAdicional_materia}}">
                        </div>
                      </div>
                  </div>
                </form>
                </div>
                @endif
            </div>
          </div>
        </div>
    </div>
  </div>
  @endsection
  
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/pdf-lib@1.4.0"></script>
<script src="https://unpkg.com/downloadjs@1.4.7"></script>
<script>
  const { PDFDocument, StandardFonts, rgb, degrees } = PDFLib
  $(document).on('click','#report-generate', async function(event) {
    const url = '{{asset('assets/docs/ticketCalculadora.pdf')}}';
    const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer());
    const pdfDoc = await PDFDocument.load(existingPdfBytes);
    const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman);
    const page = pdfDoc.getPages()[0];
    const { width, height } = page.getSize();
    const fontSize = 10
    console.log('a')
    var ts = [
      {n:'empresa',t:('{{$empresa->nombre}}' != undefined) ? '{{$empresa->nombre}}' : 'Sin Nombre',x:225,y:679,s:12},
      {n:'user',t:'{{$registro_data->usuario->name}}',x:85,y:656,s:12},
      {n:'date',t:"{{$registro_data->created_at}}",x:385,y:656,s:12},
      {n:'progname',t:('{{$producto->nombre}}' != undefined) ? '{{$producto->nombre}}' : 'Sin Nombre',x:265,y:590,s:12},
      {n:'progprice',t:'{{$producto->precio}}',x:265,y:568,s:12},
      {n:'progdescuento',t:('{{$descuentoCantidad}}' != undefined) ? '{{$descuentoCantidad}}' : '00.00',x:265,y:547,s:12},
      {n:'progdescuentoprice',t:('{{ $descuentoLabel }}'!= undefined) ? '{{ $descuentoLabel }}' : '00.00',x:400,y:547,s:12},
      {n:'progpasarela',t:('{{$pasarela->nombre}}' != undefined) ? '{{$pasarela->nombre}}' : 'Sin Pasarela',x:265,y:527,s:12},
      {n:'progcompas',t:'{{$cargoPasarela}}',x:265,y:507,s:12},
      {n:'progdescadicional',t:'{{$descuentoAdicional->descuento_adicional}}',x:265,y:484,s:12},
      {n:'progdescadicionalprice',t:'{{$descuentoAdicionalLabel}}',x:400,y:484,s:12},
      {n:'progcomision',t:'{{ $comisionLabel ?? 0}}',x:265,y:462,s:12},
      {n:'progtotal',t:document.getElementById('nuevoPrecio').value,x:265,y:437,s:12},
    ];
    @if($calculadora_data->curso)
    var tsm = [
      {n:'empresa',t:('{{$empresa->nombre}}' != undefined) ? '{{$empresa->nombre}}' : 'Sin Nombre',x:225,y:679,s:12},
      {n:'user',t:'{{$registro_data->usuario->name}}',x:85,y:656,s:12},
      {n:'date',t:"{{$registro_data->created_at}}",x:385,y:656,s:12},
      {n:'matprice',t:'{{$precioStandar_materia}}',x:265,y:584,s:12},
      {n:'matdescuento',t:('{{$descuentoCantidad_materia}}' != undefined) ? '{{$descuentoCantidad_materia}}': '00.00',x:265,y:563,s:12},
      {n:'matdescuentoprice',t:('{{$descuentoLabel_materia}}' != undefined) ? '{{$descuentoLabel_materia}}': '00.00',x:400,y:563,s:12},
      {n:'matpasarela',t:"{{$pasarelaName_materia ?? 'Sin Pasarela' }}",x:265,y:543,s:12},
      {n:'matdescadicional',t:'{{$cargoPasarela_materia}}',x:265,y:523,s:12},
      {n:'matdescadicional',t:'{{$descuentoAdicionalInput_materia}}',x:265,y:500,s:12},
      {n:'matdescadicionalprice',t:'{{$descuentoAdicionalLabel_materia}}',x:400,y:500,s:12},
      {n:'matcomision',t:'{{$comisionLabel_materia}}',x:265,y:480,s:12},
      {n:'mattotal',t:document.getElementById('nuevoPrecio_materia').value,x:265,y:457,s:12},
    ];
    @else
    var tsm = [
      {n:'empresa',t:'Sin Nombre',x:225,y:679,s:12},
      {n:'user',t:'{{$registro_data->usuario->name}}',x:85,y:656,s:12},
      {n:'date',t:"{{$registro_data->created_at}}",x:385,y:656,s:12},
      {n:'matprice',t:'00.00',x:265,y:584,s:12},
      {n:'matdescuento',t:'00.00',x:265,y:563,s:12},
      {n:'matdescuentoprice',t:'00.00',x:400,y:563,s:12},
      {n:'matpasarela',t:'Sin Pasarela',x:265,y:543,s:12},
      {n:'matdescadicional',t:'Sin Materia',x:265,y:523,s:12},
      {n:'matdescadicional',t:'00.00',x:265,y:500,s:12},
      {n:'matdescadicionalprice',t:'00.00',x:400,y:500,s:12},
      {n:'matcomision',t:'00.00',x:265,y:480,s:12},
      {n:'mattotal',t:'00.00',x:265,y:457,s:12},
    ];
    @endif

    // if(Object.keys(data_calc.prod).length<5){
    //   Swal.fire({
    //     icon: 'error',
    //     title: 'Oops...',
    //     text: 'Necesitas llenar por lo menos los datos basicos, para poder generar un ticket!',
    //     // footer: '<a href="">Why do I have this issue?</a>'
    //   })
    // }
    // else{
      Swal.fire({
      title: '¿Seguro que quieres Generar un ticket?',
      showCancelButton: true,
      confirmButtonText: 'Si',
      cancelButtonText: `Cancelar`,
      showLoaderOnConfirm: true,
      // preConfirm: (login) => {
      //   return fetch(`{{route('calculadora.saveData')}}`,
      //   {
      //     method: 'POST',
      //     body: JSON.stringify(data_calc),
      //     headers: {'Content-Type': 'application/json'}
      //   })
      //     .then(response => {
      //       if (!response.ok) {
      //         throw new Error(response.statusText)
      //       }
      //       return response.json()
      //     })
      //     .catch(error => {
      //       Swal.showValidationMessage(
      //         `Request failed: ${error}`
      //       )
      //     })
      // },
      allowOutsideClick: () => !Swal.isLoading()
      }).then(async (result) => {
        // if (result.isConfirmed) {
          console.log(result.value)
          Swal.fire({
            icon: 'success',
            title: 'Ticket Generado!',
            // imageUrl: result.value.avatar_url
          })

          $.each(ts,function(i,e){
            var v = (e.n == undefined) ? 'a' : e.t;
            var c = (e.c != undefined) ? e.c : rgb(0,0,0);
            var s = (e.s != undefined) ? e.s : 10;
            console.log(v.toString())
            page.drawText(v.toString(), {
              x: e.x,
              y: e.y,
              size: s,
              font: timesRomanFont,
              color: c,
            });
          });

          $.each(tsm,function(i,e){
            var v = (e.n == undefined) ? 'a' : e.t;
            var c = (e.c != undefined) ? e.c : rgb(0,0,0);
            var s = (e.s != undefined) ? e.s : 10;
            page.drawText(v.toString(), {
              x: e.x,
              y: e.y - 325,
              size: s,
              font: timesRomanFont,
              color: c,
            });
          });
          console.log('a')

          const pdfBytes = await pdfDoc.save()

          download(pdfBytes, "example.pdf", "application/pdf");

          Swal.close();
        // }
      })
    // }
   
  });
</script>
@endsection