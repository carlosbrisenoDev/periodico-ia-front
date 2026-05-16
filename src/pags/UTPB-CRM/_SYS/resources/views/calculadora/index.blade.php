@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  @php
    $show = '';
    if(auth()->user()->levels->alias=='administrador'){
      $show = '';
    }
    elseif(auth()->user()->levels->alias=='ventas'){
      $show = 'd-none';
    }
    

  @endphp
  <div class="card">
    <div class="card-body">
      <button class="btn btn-info" id="report-generate">Imprimir / Generar Reporte</button>
      <a class="btn btn-warning" href="{{route('calculadora.misDatos')}}">Ver mis calculos/tickets</a>
      @if(auth()->user()->levels->alias=='administrador')
        <a class="btn btn-danger" href="{{route('calculadora.datosGenerales')}}">Ver calculos/tickets Generados por otros usuarios</a>
        {{-- a --}}
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
                        <select class="form-control form-control-sm" id="empresa">
                            <option>Elije una opción</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-12 mb-4">
                        <label for="producto">Producto</label>
                        <select class="form-control form-control-sm" id="producto" disabled>
                            <option>Elije una opción</option>
                        </select>
                    </div>

                    <div class="form-group row mb-4">
                        <label for="precioEstandar" class="col-sm-5 col-form-label">Precio Estandar</label>
                        <div class="col-sm-6">
                          <input type="text" class="form-control" id="precioEstandar" placeholder="00.00" disabled >
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                      <label for="descuento" class="col-sm-5 col-form-label">Agregar Descuento</label>
                      <div class="col-sm-2">
                        <input type="text" class="form-control" id="descuento" placeholder="00 %" disabled >
                      </div>
                      <label for="descuento" class="col-sm-4 col-form-label">Maximo descuento: <span id="maxDescuento">N/A</span></label>
                    </div>

                    <div class="form-group row mb-4">
                        <label for="descuentoField" class="col-sm-5 col-form-label">Descuento</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="descuentoField" placeholder="00.00" disabled>
                        </div>
                    </div>

                    <div class="form-group row mb-4 {{$show}}">
                        <label for="costo" class="col-sm-5 col-form-label">Costo</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="costo" placeholder="00.00" disabled>
                        </div>
                    </div>

                    <label for="pasarelas" class="col-form-label">Elije una pasarela</label>
                    <div class="form-group mb-4" id="pasarelas">
                      
                      
                    </div>

                    <div class="form-group row mb-4 {{$show}}">
                        {{-- <label for="pasarelaComision" class="col-sm-3 col-form-label">Pasarela Comisión</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control" id="pasarelaComision" placeholder="00.00" disabled>
                        </div> --}}

                        <label for="pasarelaComisionFija" class="col-sm-5 col-form-label">Pasarela Fija</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="pasarelaComisionFija" placeholder="00.00" disabled>
                        </div>
                    </div>

                    <div class="form-group row mb-4 {{$show}}">
                      <label for="pasarelaComision" class="col-sm-5 col-form-label">Pasarela Comisión</label>
                      <div class="col-sm-5">
                        <input type="text" class="form-control" id="pasarelaComision" placeholder="00.00" disabled>
                      </div>

                      {{-- <label for="pasarelaComisionFija" class="col-sm-3 col-form-label">Pasarela Fija</label>
                      <div class="col-sm-2">
                        <input type="text" class="form-control" id="pasarelaComisionFija" placeholder="00.00" disabled>
                      </div> --}}
                  </div>

                    <div class="form-group row mb-4">
                      <label for="pasarelaComisionTotal" class="col-sm-5 col-form-label">Pasarela Cargos</label>
                      <div class="col-sm-5">
                        <input type="text" class="form-control" id="pasarelaComisionTotal" placeholder="00.00" disabled>
                      </div>
                    </div>

                    <div class="form-group row mb-4 {{$show}}">
                        <label for="beneficio" class="col-sm-5 col-form-label">Beneficio</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="beneficio" placeholder="00.00" disabled>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                        {{-- <label for="descuentoAdicional" class="col-sm-5 col-form-label">Descuento Adicional</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="descuentoAdicional" placeholder="00.00" disabled>
                        </div> --}}

                        <label for="tipoDescuento" class="col-sm-5 col-form-label">Tipo Descuento</label>
                        <div class="col-sm-5">
                            <select class="form-control form-control-sm" id="tipoDescuento" disabled>
                                <option value="1">Porcentaje</option>
                                <option value="2">Precio Fijo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                      <label for="descuentoAdicional" class="col-sm-5 col-form-label">Descuento Adicional</label>
                      <div class="col-sm-5">
                        <input type="text" class="form-control" id="descuentoAdicional" placeholder="00.00" disabled>
                      </div>

                      {{-- <label for="tipoDescuento" class="col-sm-5 col-form-label">Tipo Descuento</label>
                      <div class="col-sm-5">
                          <select class="form-control form-control-sm" id="tipoDescuento" disabled>
                              <option value="1">Porcentaje</option>
                              <option value="2">Precio Fijo</option>
                          </select>
                      </div> --}}
                  </div>

                    <div class="form-group row mb-4">
                        <label for="descuentoAdicionalField" class="col-sm-5 col-form-label">Descuento</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="descuentoAdicionalField" placeholder="00.00" disabled>
                        </div>
                    </div>

                    <div class="form-group row mb-4 {{$show}}">
                        <label for="beneficioFinal" class="col-sm-5 col-form-label">Beneficio Final</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" id="beneficioFinal" placeholder="00.00" disabled style="background-color: rgba(255,205,0,0.3);">
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                        {{-- <label for="utilidad" class="col-sm-2 col-form-label">Utilidad</label>
                        <div class="col-sm-4">
                          <input type="text" class="form-control" id="utilidad" placeholder="00.00" disabled>
                        </div> --}}

                        <label for="comision" class="col-sm-5 col-form-label">Comisión</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="comision" placeholder="00.00" disabled>
                        </div>
                    </div>

                    <div class="form-group row mb-4 {{$show}}">
                      <label for="utilidad" class="col-sm-5 col-form-label">Utilidad</label>
                      <div class="col-sm-5">
                        <input type="text" class="form-control" id="utilidad" placeholder="00.00" disabled>
                      </div>

                      {{-- <label for="comision" class="col-sm-2 col-form-label">Comisión</label>
                      <div class="col-sm-4">
                          <input type="text" class="form-control" id="comision" placeholder="00.00" disabled>
                      </div> --}}
                  </div>

                    <div class="form-group row mb-4">
                      <label for="nuevoPrecio" class="col-sm-5 col-form-label">Precio Final Para Comprador</label>
                      <div class="col-sm-12">
                        <input type="text" class="form-control" id="nuevoPrecio" placeholder="00.00" disabled style="background-color: rgba(0,127,0,0.4);">
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
              <div class="card-body">
                  <form>

                      <div class="form-group col-12 mb-4">
                          <label for="course">Tipo de Cursamiento</label>
                          <select class="form-control form-control-sm" id="course" disabled>
                              <option>Elije una opción</option>
                              <option value='1'>Pago Por materia</option>
                              <option value='2'>Colegiatura</option>
                          </select>
                          <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <small class="text-white">El select esta deshabilitado hasta que elijas un producto, para hacer el calculo si es pago por materia o colegiatura</small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true" class="text-white">X</span>
                            </button>
                        </div>
                          
                      </div>
                      <div id="info-course" style="display: none;">
                        {{-- <div class="form-group col-12 mb-4" id="materia-select-label">
                            <label for="materia">Materia</label>
                            <select class="form-control form-control-sm" id="materia" disabled>
                                <option>Elije una opción</option>
                            </select>
                        </div> --}}

                        <div class="form-group row mb-4">
                            <label for="precioEstandar_materia" class="col-sm-5 col-form-label" id="precioEstandar_materiaLabel">Precio Estandar de Materia</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="precioEstandar_materia" placeholder="00.00" disabled >
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                          <label for="descuento_materia" class="col-sm-5 col-form-label" id="descuento_materiaLabel">Agregar Descuento de Materia</label>
                          <div class="col-sm-2">
                            <input type="text" class="form-control" id="descuento_materia" placeholder="00 %" disabled >
                          </div>
                          <label for="maxDescuento_materia" class="col-sm-4 col-form-label">Maximo descuento: <span id="maxDescuento_materia">N/A</span></label>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="descuentoField_materia" class="col-sm-5 col-form-label" id="descuentoField_materiaLabel">Descuento de Materia</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="descuentoField_materia" placeholder="00.00" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-4 {{$show}}">
                            <label for="costo_materia" class="col-sm-5 col-form-label" id="costo_materiaLabel">Costo de Materia</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="costo_materia" placeholder="00.00" disabled>
                            </div>
                        </div>

                        <label for="pasarelas_materia" class="col-form-label" id="pasarelas_materiaLabel">Elije una pasarela</label>
                        <div class="form-group mb-4" id="pasarelas_materia">
                          
                        </div>
                        
                        <div class="form-group row mb-4 {{$show}}">

                            <label for="pasarelaComisionFija_materia" class="col-sm-5 col-form-label" id="pasarelaComisionFija_materiaLabel">Pasarela Fija de Materia</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="pasarelaComisionFija_materia" placeholder="00.00" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-4 {{$show}}">
                          <label for="pasarelaComision_materia" class="col-sm-5 col-form-label" id="pasarelaComision_materiaLabel">Pasarela Comisión de Materia</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="pasarelaComision_materia" placeholder="00.00" disabled>
                          </div>
                        </div>

                        <div class="form-group row mb-3">
                          <label for="pasarelaComisionTotal_materia" class="col-sm-5 col-form-label" id="pasarelaComisionTotal_materiaLabel">Pasarela Cargos de Materia</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="pasarelaComisionTotal_materia" placeholder="00.00" disabled>
                          </div>
                        </div>

                        
                        <div class="form-group row mb-4 {{$show}}">
                            <label for="beneficio_materia" class="col-sm-5 col-form-label" id="beneficio_materiaLabel">Beneficio de Materia</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="beneficio_materia" placeholder="00.00" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                          <label for="descuentoAdicional_materia" class="col-sm-5 col-form-label" id="descuentoAdicional_materiaLabel">Descuento Adicional de Materia</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="descuentoAdicional_materia" placeholder="00.00" disabled>
                          </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="tipoDescuento_materia" class="col-sm-5 col-form-label" id="tipoDescuento_materiaLabel">Tipo Descuento de Materia</label>
                            <div class="col-sm-5">
                                <select class="form-control form-control-sm" id="tipoDescuento_materia" disabled>
                                    <option value="1">Porcentaje</option>
                                    <option value="2">Precio Fijo</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="descuentoAdicionalField_materia" class="col-sm-5 col-form-label" id="descuentoAdicionalField_materiaLabel">Descuento de Materia</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="descuentoAdicionalField_materia" placeholder="00.00" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-4 {{$show}}">
                            <label for="beneficioFinal_materia" class="col-sm-5 col-form-label" id="beneficioFinal_materiaLabel">Beneficio Final de Materia</label>
                            <div class="col-sm-5">
                              <input type="text" class="form-control" id="beneficioFinal_materia" placeholder="00.00" disabled style="background-color: rgba(255,205,0,0.3);">
                            </div>
                        </div>

                        <div class="form-group row mb-4" {{$show}}>
                            <label for="comision_materia" class="col-sm-5 col-form-label" id="comision_materiaLabel">Comisión <span id="comision-bussiness"></span></label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="comision_materia" placeholder="00.00" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-4" {{$show}}>
                          <label for="utilidad_materia" class="col-sm-5 col-form-label" id="utilidad_materiaLabel">Utilidad de Materia</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="utilidad_materia" placeholder="00.00" disabled>
                          </div>
                        </div>

                        <div class="form-group row mb-4">
                          <label for="nuevoPrecio_materia" class="col-sm-12 col-form-label" id="nuevoPrecio_materiaLabel">Precio Final Para Comprador de Materia</label>
                          <div class="col-sm-12">
                            <input type="text" class="form-control" id="nuevoPrecio_materia" placeholder="00.00" disabled style="background-color: rgba(0,127,0,0.4);">
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
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/pdf-lib@1.4.0"></script>
<script src="https://unpkg.com/downloadjs@1.4.7"></script>
{{-- <script type="text/javascript" src="{{asset('/js/calculadora_data.js')}}"></script> --}}
<script>
  const { PDFDocument, StandardFonts, rgb, degrees } = PDFLib
  let data_calc = {'_token':'{{csrf_token()}}'};
  
  let pasarela_content = "", desucentos_content = "", productos_content = "",empresas_content="",materias_content="";
  var empresaData = {}, prod = {},desc = {},pas = {},desc_ad = {"tipo_descuento":1,'descuento_adicional':0};
  var beneficio = 0, utilidad = 0,benef_final = 0,precio_standar=0,descuento=0,pasarelaCargo=0,descuentoAdicional=0,nuevoPrecio=0,costo=0,comisionOrg=0,comision=0,beneficioFinal=0,nuevoPrecioWDescuento=0;
  
  let pasarela_content_materia = "", desucentos_content_materia = "", productos_content_materia = "",empresas_content_materia="",materias_content_materia="";
  var empresaData_materia = {}, matr = {},desc_materia = {},pas_materia = {},desc_ad_materia = {"tipo_descuento":1,'descuento_adicional':0};
  var tipo_comision_materia='', beneficio_materia = 0, utilidad_materia = 0,benef_final_materia = 0,precio_standar_materia=0,descuento_materia=0,pasarelaCargo_materia=0,descuentoAdicional_materia=0,nuevoPrecio_materia=0,costo_materia=0,comisionOrg_materia=0,comision_materia=0,beneficioFinal_materia=0,nuevoPrecioWDescuento_materia=0;

  $(document).ready(async function(){
    let empresa_query = null, pasarela_query = null, productos_query = null, materias_query=null;
    async function serverResponse(param={},url) {
      const result = await $.ajax({
        url: url,
        type: 'POST',
        data: param,
      })
      return result
    }

    empresa_query = await serverResponse({_token:'{{csrf_token()}}'},'/api/empresas/getAll');
    pasarela_query = await serverResponse({_token:'{{csrf_token()}}'},'/api/pasarelas/getAll');    
    
    empresa_query.forEach(async function(element){
      empresas_content += `<option value="${element.slug}">${element.nombre}</option>`;
    });
    
    pasarela_query.forEach(async function(element){
      pasarela_content += `<div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="pasarelaChose" id="${element.id}" value="${element.id}" disabled>
        <label class="form-check-label" for="${element.id}">${element.nombre}</label>
      </div> `;
    });
    
    document.getElementById('pasarelas').innerHTML = pasarela_content;
    document.getElementById('empresa').innerHTML += empresas_content
    document.getElementById('comision').value = "$ " +comision.toFixed(2);

    ////////////////// ELECCIÓN DE PRODUCTO

    document.getElementById('empresa').addEventListener('change',async function(){
      var cur_label = "";
      if(this.value=="Elije una opción"){
        resetAll();
        document.getElementById("producto").setAttribute('disabled','diisabled');
      }
      else{
        resetAll();
        empresaData = empresa_query.find(element => element.slug === this.value);
        data_calc.empresaData = empresaData;
        productos_query = await serverResponse({_token:'{{csrf_token()}}', slug:this.value},'/api/productos/getAll');
        productos_content = '<option>Elije una opción</option>';
        productos_query.forEach(function(element){
          if(cur_label!=element.tipo){
            productos_content += `</optgroup>`;
            productos_content += `<optgroup label="${element.tipo}">`;
          }
          cur_label = element.tipo;
          productos_content += `<option value="${element.id}">${element.tipo} ${element.nombre}</option>`;

        });
        document.getElementById('producto').innerHTML = productos_content;
        document.getElementById("producto").removeAttribute('disabled');
        document.getElementById("comision-bussiness").innerHTML = empresaData.slug;
      }
    });

    document.getElementById('producto').addEventListener('change', async function() {
      
      if(this.value=="Elije una opción"){
        resetAll();
        document.getElementById('precioEstandar').value = "$ 00.00";
        document.getElementsByName("discount").forEach(element => element.setAttribute('disabled',""));
        beneficio = 0;
        precio_standar = 0;
      }
      else{
        prod = productos_query.find(element => element.id === parseInt(this.value));
        data_calc.prod = prod;
        // materias_query = await serverResponse({_token:'{{csrf_token()}}', id:prod.id},'/api/materias/getAll');
        // materias_content = '<option>Elije una opción</option>';
        // materias_query.forEach(function(element){
        //   cur_label = element.tipo;
        //   materias_content += `<option value="${element.id}">${element.asignatura}</option>`;

        // });
        // document.getElementById('materia').innerHTML = materias_content;
        document.getElementById("course").removeAttribute('disabled');
        precio_standar = prod.precio;
        nuevoPrecio = prod.precio;
        nuevoPrecioWDescuento = prod.precio;
        costo=prod.costo;
        comision=prod.comision;
        comisionOrg=prod.comision;
        document.getElementById('nuevoPrecio').value = "$ " +prod.precio.toFixed(2)+"";
        desc_calc()
        pasarela_calc()
        desc_ad_calc()
        benef_calc()
        benef_final_calc()
        comision_calc()
        utilidad_calc()
        document.getElementById('precioEstandar').value = "$ " +prod.precio.toFixed(2)+"";
        document.getElementsByName("discount").forEach(element => element.removeAttribute('disabled'));
        document.getElementsByName("pasarelaChose").forEach(element => element.removeAttribute('disabled'));
        document.getElementById("descuentoAdicional").removeAttribute('disabled');
        document.getElementById("tipoDescuento").removeAttribute('disabled');
        document.getElementById('descuento').removeAttribute('disabled');
        document.getElementById('maxDescuento').innerHTML = prod.descuento_max+" %";
        // document.getElementById('nuevoPrecio').value = "$ " +prod.precio.toFixed(2)+"";
        document.getElementById('costo').value = "$ " +prod.costo.toFixed(2)+"";
        
      }
      
    });

    document.getElementById('descuento').addEventListener('keyup', async function() {
      if(parseInt(this.value)>prod.descuento_max){
        this.value = prod.descuento_max;
      }
      desc = {"tipo":prod.tipo_descuento, "cantidad":this.value};
      data_calc.desc = desc;
      descuento = desc.cantidad;
      desc_calc()
      desc_ad_calc()
      pasarela_calc()
      benef_calc()
      benef_final_calc()
      comision_calc()
      utilidad_calc()
    });

    ////////////////// ELECCIÓN DE DESCUENTO
    // document.getElementsByName('discount').forEach(el => el.addEventListener('click', event => {
    //   desc = datos.descuentos.find(element => element.id === parseInt(event.path[0].value));
    //   descuento = desc.cantidad;
    //   desc_calc()
    //   pasarela_calc()
    //   desc_ad_calc()
    //   benef_calc()
    //   benef_final_calc()
    //   comision_calc()
    //   utilidad_calc()
    // }));

    ////////////////// ELECCIÓN DE PASARELA
    document.getElementsByName('pasarelaChose').forEach(el => el.addEventListener('click', event => {
      let path = event.path || event.composedPath()
      pas = pasarela_query.find(element => element.id === parseInt(path[0].value));
      data_calc.pas = pas;
      pasarela_calc()
      benef_calc()
      benef_final_calc()
      comision_calc()
      utilidad_calc()
    }));

    ////////////////// ELECCIÓN DE DESCUENTO ADICIONAL
    document.getElementById('descuentoAdicional').addEventListener('keyup', function() {
      desc_ad['descuento_adicional'] = this.value;
      data_calc.desc_ad['descuento_adicional'] = this.value;
      descuentoAdicional = this.value;
      desc_calc()
      desc_ad_calc()
      pasarela_calc()
      benef_calc()
      benef_final_calc()
      comision_calc()
      utilidad_calc()
    });

    ////////////////// ELECCIÓN DE TIPO DE DESCUENTO
    document.getElementById('tipoDescuento').addEventListener('change', function() {
      desc_ad['tipo_descuento'] = this.value;
      data_calc.desc_ad['tipo_descuento'] = this.value;
      desc_ad_calc()
      benef_calc()
      benef_final_calc()
      comision_calc()
      utilidad_calc()
    });

    function comision_calc(){
      if(desc_ad.descuento_adicional!=0){
          comision = (beneficioFinal)*(((comisionOrg/precio_standar)*100)/100);
          document.getElementById('comision').value = "$ " +comision.toFixed(2);
      }
    }

    function benef_calc(){
      beneficio = precio_standar-costo-pasarelaCargo-descuento;
      document.getElementById('beneficio').value = "$ " +beneficio.toFixed(2)+"";
    }

    function desc_calc(){
      if(Object.keys(desc).length!=0){
        descuento = desc.cantidad;
        if(desc.tipo == "$"){
          var discount = parseFloat(descuento);
          var preciowdiscount = precio_standar - descuento;
        }
        if(desc.tipo == "%"){
          var discount = parseFloat(precio_standar*parseFloat(descuento/100));
          var preciowdiscount = precio_standar - (precio_standar*parseFloat(descuento/100));
        }
        document.getElementById('descuentoField').value = "$ " +discount.toFixed(2)+"";
        document.getElementById('nuevoPrecio').value = "$ " +preciowdiscount.toFixed(2)+"";
        nuevoPrecio = preciowdiscount;
        nuevoPrecioWDescuento = preciowdiscount;
        descuento = discount;
      } 
      else{
        nuevoPrecio = precio_standar;
        document.getElementById('nuevoPrecio').value = "$ " +nuevoPrecio.toFixed(2)+"";
      }
    }

    function desc_ad_calc(){
      if(desc_ad.descuento_adicional!=0 && desc_ad.descuento_adicional!=''){
        descuentoAdicional = desc_ad.descuento_adicional;
        
        if(desc_ad.tipo_descuento == 2){
          var discount = descuentoAdicional;
          var preciowdiscount = beneficio - descuentoAdicional;
        }
        if(desc_ad.tipo_descuento == 1){
          var discount = beneficio*parseFloat(descuentoAdicional/100);
          var preciowdiscount = beneficio - (beneficio*parseFloat(descuentoAdicional/100));
        }
        document.getElementById('descuentoAdicionalField').value = "$ " +parseInt(discount).toFixed(2)+"";
        document.getElementById('nuevoPrecio').value = "$ " +preciowdiscount.toFixed(2)+"";
        nuevoPrecio = preciowdiscount;
        descuentoAdicional = discount;
      }
      else{
          comision = comisionOrg;
          descuentoAdicional = 0;
          document.getElementById('descuentoAdicionalField').value = "$ " +parseInt(descuentoAdicional).toFixed(2)+"";
          document.getElementById('comision').value = "$ " +comision.toFixed(2);
      }
      console.table('descuentoAdicional: '+descuentoAdicional)
    }

    function benef_final_calc(){
      beneficioFinal = beneficio-descuentoAdicional; 
      document.getElementById('beneficioFinal').value = "$ " +beneficioFinal.toFixed(2)+"";
    }

    function utilidad_calc(){
      utilidad=beneficioFinal-comision;
      if(utilidad<=500){
        utilidad=500;
        comision = beneficioFinal-utilidad;
        document.getElementById('comision').value = "$ " +comision.toFixed(2);
      }
      document.getElementById('utilidad').value = "$ " +utilidad.toFixed(2)+"";
    }

    function resetAll(){
      beneficio = 0, utilidad = 0,benef_final = 0,precio_standar=0,descuento=0,pasarelaCargo=0,descuentoAdicional=0,nuevoPrecio=0,costo=0,beneficioFinal=0,nuevoPrecioWDescuento=0;
      prod = {},desc = {},pas = {},desc_ad = {"tipo_descuento":1,'descuento_adicional':0};
      data_calc.prod = {},data_calc.desc = {},data_calc.pas = {},data_calc.desc_ad = {"tipo_descuento":1,'descuento_adicional':0};
      desc_calc()
      desc_ad_calc()
      benef_calc()
      benef_final_calc()
      utilidad_calc()
      document.getElementById('precioEstandar').value = "$ 00.00";
      document.getElementsByName("discount").forEach(element => element.setAttribute('disabled',''));
      document.getElementsByName("pasarelaChose").forEach(element => element.setAttribute('disabled',''));
      document.getElementById("descuentoAdicional").setAttribute('disabled','');
      document.getElementById("tipoDescuento").setAttribute('disabled','');
      document.getElementById('descuento').setAttribute('disabled','');
      document.getElementsByName("discount").forEach(element => element.setAttribute('disabled',""));
      document.getElementById('nuevoPrecio').value = "$ 00.00";
      document.getElementById('costo').value = "$ 00.00";      
    }
    
    function pasarela_calc(){
        if(Object.keys(pas).length!=0){
          document.getElementById('pasarelaComision').value = pas.comision+" %";
          pasarelaCargo = ((nuevoPrecio)*parseFloat(pas.comision/100)+pas.comision_fija)+(((nuevoPrecio)*parseFloat(pas.comision/100)+pas.comision_fija)*(pas.iva/100));
          document.getElementById('pasarelaComisionTotal').value = "$ "+pasarelaCargo.toFixed(2);
          document.getElementById('pasarelaComisionFija').value = "$ "+pas.comision_fija.toFixed(2);
        }
    }

    ///////// CALCULADORA DE MATERIA ////////////////

    pasarela_query.forEach(async function(element){
      pasarela_content_materia += `<div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="pasarelaChose_materia" id="${element.id}_materia" value="${element.id}" disabled>
        <label class="form-check-label" for="${element.id}_materia">${element.nombre}</label>
      </div> `;
    });

    // course
    document.getElementById('course').addEventListener('change', async function() {
      resetAll_materia();
      document.getElementById('precioEstandar_materia').value = "$ 00.00";
      document.getElementsByName("discount_materia").forEach(element => element.setAttribute('disabled',""));
      beneficio_materia = 0;
      precio_standar_materia = 0;
      if(this.value=="Elije una opción"){
        // resetAll_materia();
        document.getElementById('info-course').style.display = 'none';
        document.getElementById('precioEstandar_materia').value = "$ 00.00";
        document.getElementsByName("discount_materia").forEach(element => element.setAttribute('disabled',""));
        // beneficio_materia = 0;
        // precio_standar_materia = 0;
      }
      if(this.value=='1'){
        data_calc.curso = 'materia';
        document.getElementById('info-course').style.display = 'block';
        // document.getElementById('materia').removeAttribute('disabled');
        // document.getElementById('materia-select-label').style.display = 'block';
        console.log('a')
        document.getElementById('precioEstandar_materiaLabel').innerHTML= 'Precio Estandar de Materia';
        document.getElementById('descuento_materiaLabel').innerHTML= 'Agregar Descuento de Materia';
        document.getElementById('descuentoField_materiaLabel').innerHTML= 'Descuento de Materia';
        document.getElementById('costo_materiaLabel').innerHTML= 'Costo de Materia';
        // document.getElementById('pasarelas_materiaLabel').innerHTML= 'Elije una pasarela';
        document.getElementById('pasarelaComisionFija_materiaLabel').innerHTML= 'Pasarela Fija de Materia';
        document.getElementById('pasarelaComision_materiaLabel').innerHTML= 'Pasarela Comisión de Materia';
        document.getElementById('pasarelaComisionTotal_materiaLabel').innerHTML= 'Pasarela Cargos de Materia';
        document.getElementById('beneficio_materiaLabel').innerHTML= 'Beneficio de Materia';
        document.getElementById('descuentoAdicional_materiaLabel').innerHTML= 'Descuento Adicional de Materia';
        document.getElementById('tipoDescuento_materiaLabel').innerHTML= 'Tipo Descuento de Materia';
        document.getElementById('descuentoAdicionalField_materiaLabel').innerHTML= 'Descuento de Materia';
        document.getElementById('beneficioFinal_materiaLabel').innerHTML= 'Beneficio Final de Materia';
        // document.getElementById('comision_materiaLabel').innerHTML= 'Comisión <span id="comision-bussiness"></span> de Materia';
        document.getElementById('utilidad_materiaLabel').innerHTML= 'Utilidad de Materia';
        document.getElementById('nuevoPrecio_materiaLabel').innerHTML= 'Precio Final Para Comprador de Materia';
        resetAll_materia();
        precio_standar_materia = prod.precio_mensualidad;
        nuevoPrecio_materia = prod.precio_mensualidad;
        nuevoPrecioWDescuento_materia = prod.precio_mensualidad;
        costo_materia=prod.costo_mensualidad;
        comision_materia=prod.comision_materia;
        comisionOrg_materia=prod.comision_materia;
        tipo_comision_materia=prod.tipo_comision_materia;
        descuentoMax_materia = prod.descuento_max_materia;
        tipoDescuento_materia = prod.tipo_descuento_materia;
        document.getElementById('nuevoPrecio_materia').value = "$ " +precio_standar_materia.toFixed(2)+"";
        desc_calc_materia();
        pasarela_calc_materia();
        desc_ad_calc_materia();
        benef_calc_materia();
        benef_final_calc_materia();
        comision_calc_materia();
        utilidad_calc_materia();
        document.getElementById('precioEstandar_materia').value = "$ " +precio_standar_materia.toFixed(2)+"";
        document.getElementsByName("discount_materia").forEach(element => element.removeAttribute('disabled'));
        document.getElementById("descuentoAdicional_materia").removeAttribute('disabled');
        document.getElementById("tipoDescuento_materia").removeAttribute('disabled');
        document.getElementById('descuento_materia').removeAttribute('disabled');
        document.getElementById('maxDescuento_materia').innerHTML = descuentoMax_materia+" %";
        document.getElementById('costo_materia').value = "$ " +costo_materia.toFixed(2)+"";
        document.getElementById('pasarelas_materia').innerHTML = pasarela_content_materia;
        document.getElementsByName("pasarelaChose_materia").forEach(element => element.removeAttribute('disabled'));
        document.getElementById('comision_materia').value = "$ " +comision_materia.toFixed(2);
      }
      if(this.value=='2'){
        data_calc.curso = 'colegiatura';
        document.getElementById('info-course').style.display = 'block';
        // document.getElementById('materia').setAttribute('disabled','disabled');
        // document.getElementById('materia-select-label').style.display = 'none';
        document.getElementById('precioEstandar_materiaLabel').innerHTML= 'Precio Mensualidad';
        document.getElementById('descuento_materiaLabel').innerHTML= 'Agregar Descuento Mensualidad';
        document.getElementById('descuentoField_materiaLabel').innerHTML= 'Descuento Mensualidad';
        document.getElementById('costo_materiaLabel').innerHTML= 'Costo Mensualidad';
        // document.getElementById('pasarelas_materiaLabel').innerHTML= 'none';
        document.getElementById('pasarelaComisionFija_materiaLabel').innerHTML= 'Pasarela Fija de Mensualidad';
        document.getElementById('pasarelaComision_materiaLabel').innerHTML= 'Pasarela Comisión de Mensualidad';
        document.getElementById('pasarelaComisionTotal_materiaLabel').innerHTML= 'Pasarela Cargos de Mensualidad';
        document.getElementById('beneficio_materiaLabel').innerHTML= 'Beneficio de Mensualidad';
        document.getElementById('descuentoAdicional_materiaLabel').innerHTML= 'Descuento Adicional de Mensualidad';
        document.getElementById('tipoDescuento_materiaLabel').innerHTML= 'Tipo Descuento de Mensualidad';
        document.getElementById('descuentoAdicionalField_materiaLabel').innerHTML= 'Descuento de Mensualidad';
        document.getElementById('beneficioFinal_materiaLabel').innerHTML= 'Beneficio Final de Mensualidad';
        // document.getElementById('comision_materiaLabel').innerHTML= 'none';
        document.getElementById('utilidad_materiaLabel').innerHTML= 'Utilidad de Mensualidad';
        document.getElementById('nuevoPrecio_materiaLabel').innerHTML= 'Precio Final Para Comprador de Mensualidad';
        
        resetAll_materia();
        precio_standar_materia = prod.precio_mensualidad;
        nuevoPrecio_materia = prod.precio_mensualidad;
        nuevoPrecioWDescuento_materia = prod.precio_mensualidad;
        costo_materia=prod.costo_mensualidad;
        comision_materia=prod.comision_mensualidad;
        comisionOrg_materia=prod.comision_mensualidad;
        tipo_comision_materia=prod.tipo_comision_mensualidad;
        descuentoMax_materia = prod.descuento_max_mensualidad;
        tipoDescuento_materia = prod.tipo_descuento_mensualidad;
        document.getElementById('nuevoPrecio_materia').value = "$ " +precio_standar_materia.toFixed(2)+"";
        desc_calc_materia();
        pasarela_calc_materia();
        desc_ad_calc_materia();
        benef_calc_materia();
        benef_final_calc_materia();
        comision_calc_materia();
        utilidad_calc_materia();
        document.getElementById('precioEstandar_materia').value = "$ " +precio_standar_materia.toFixed(2)+"";
        document.getElementsByName("discount_materia").forEach(element => element.removeAttribute('disabled'));
        document.getElementById("descuentoAdicional_materia").removeAttribute('disabled');
        document.getElementById("tipoDescuento_materia").removeAttribute('disabled');
        document.getElementById('descuento_materia').removeAttribute('disabled');
        document.getElementById('maxDescuento_materia').innerHTML = descuentoMax_materia+" %";
        document.getElementById('costo_materia').value = "$ " +costo_materia.toFixed(2)+"";
        document.getElementById('pasarelas_materia').innerHTML = pasarela_content_materia;
        document.getElementsByName("pasarelaChose_materia").forEach(element => element.removeAttribute('disabled'));
        document.getElementById('comision_materia').value = "$ " +comision_materia.toFixed(2);
      }
      
    });

    // document.getElementById('materia').addEventListener('change', async function() {
      
    //   if(this.value=="Elije una opción"){
    //     resetAll_materia();
    //     document.getElementById('precioEstandar_materia').value = "$ 00.00";
    //     document.getElementsByName("discount_materia").forEach(element => element.setAttribute('disabled',""));
    //     beneficio_materia = 0;
    //     precio_standar_materia = 0;
    //   }
    //   else{
    //     resetAll_materia();
    //     matr = materias_query.find(element => element.id === parseInt(this.value));
    //     data_calc.matr = matr;
    //     precio_standar_materia = matr.precio;
    //     nuevoPrecio_materia = matr.precio;
    //     nuevoPrecioWDescuento_materia = matr.precio;
    //     costo_materia=matr.costo;
    //     comision_materia=matr.comision;
    //     comisionOrg_materia=matr.comision;
    //     tipo_comision_materia=matr.tipo_comision
    //     descuentoMax_materia = matr.descuento_max;
    //     tipoDescuento_materia = matr.tipo_descuento;
    //     document.getElementById('nuevoPrecio_materia').value = "$ " +matr.precio.toFixed(2)+"";
    //     desc_calc_materia();
    //     pasarela_calc_materia();
    //     desc_ad_calc_materia();
    //     benef_calc_materia();
    //     benef_final_calc_materia();
    //     comision_calc_materia();
    //     utilidad_calc_materia();
    //     document.getElementById('precioEstandar_materia').value = "$ " +matr.precio.toFixed(2)+"";
    //     document.getElementsByName("discount_materia").forEach(element => element.removeAttribute('disabled'));
    //     document.getElementById("descuentoAdicional_materia").removeAttribute('disabled');
    //     document.getElementById("tipoDescuento_materia").removeAttribute('disabled');
    //     document.getElementById('descuento_materia').removeAttribute('disabled');
    //     document.getElementById('maxDescuento_materia').innerHTML = descuentoMax_materia+" %";
    //     document.getElementById('costo_materia').value = "$ " +matr.costo.toFixed(2)+"";
    //     document.getElementById('pasarelas_materia').innerHTML = pasarela_content_materia;
    //     document.getElementsByName("pasarelaChose_materia").forEach(element => element.removeAttribute('disabled'));
    //     document.getElementById('comision_materia').value = "$ " +comision_materia.toFixed(2);
    //   }
      
    // });

    document.getElementById('descuento_materia').addEventListener('keyup', function() {
      if(parseInt(this.value)>descuentoMax_materia){
        this.value = descuentoMax_materia;
      }
      desc_materia = {"tipo":tipoDescuento_materia, "cantidad":this.value};
      data_calc.desc_materia = desc_materia;
      descuento_materia = desc_materia.cantidad;
      desc_calc_materia()
      desc_ad_calc_materia()
      pasarela_calc_materia()
      benef_calc_materia()
      benef_final_calc_materia()
      comision_calc_materia()
      utilidad_calc_materia()
    });

    ////////////////// ELECCIÓN DE DESCUENTO
    // document.getElementsByName('discount_materia').forEach(el => el.addEventListener('click', event => {
    //   desc_materia = datos.descuentos.find(element => element.id === parseInt(event.path[0].value));
    //   descuento_materia = desc.cantidad;
    //   desc_calc_materia()
    //   desc_ad_calc_materia()
    //   pasarela_calc_materia()
    //   benef_calc_materia()
    //   benef_final_calc_materia()
    //   comision_calc_materia()
    //   utilidad_calc_materia()
      
    // }));

    ////////////////// ELECCIÓN DE PASARELA
    $(document).on('click', "input[name='pasarelaChose_materia']", function() {
      console.log('a')
      // let path = event.path || event.composedPath()
      pas_materia = pasarela_query.find(element => element.id === parseInt($(this).val()));
      data_calc.pas_materia = pas_materia;
      pasarela_calc_materia()
      benef_calc_materia()
      benef_final_calc_materia()
      comision_calc_materia()
      utilidad_calc_materia()
    });

    ////////////////// ELECCIÓN DE DESCUENTO ADICIONAL
    document.getElementById('descuentoAdicional_materia').addEventListener('keyup', function() {
      desc_ad_materia['descuento_adicional'] = this.value;
      data_calc.desc_ad_materia['descuento_adicional'] = this.value;
      descuentoAdicional_materia = this.value;
      console.log('putamadre abuela')
      desc_calc_materia()
      desc_ad_calc_materia()
      pasarela_calc_materia()
      benef_calc_materia()
      benef_final_calc_materia()
      comision_calc_materia()
      utilidad_calc_materia()
    });

    ////////////////// ELECCIÓN DE TIPO DE DESCUENTO
    document.getElementById('tipoDescuento_materia').addEventListener('change', function() {
      desc_ad_materia['tipo_descuento'] = this.value;
      data_calc.desc_ad_materia['tipo_descuento'] = this.value;
      desc_ad_calc_materia()
      benef_calc_materia()
      benef_final_calc_materia()
      comision_calc_materia()
      utilidad_calc_materia()
    });

    function comision_calc_materia(){
      console.log('qie')
      if(tipo_comision_materia=='%'){
        comision_materia = (beneficio_materia)*((comisionOrg_materia)/100);
        document.getElementById('comision_materia').value = "$ " +comision_materia.toFixed(2);
      }
      if(tipo_comision_materia=='$'){
        comision_materia = (beneficio_materia)*(((comisionOrg_materia/precio_standar_materia)*100)/100);
        document.getElementById('comision_materia').value = "$ " +comision_materia.toFixed(2);
      }
    }

    function benef_calc_materia(){
      beneficio_materia = precio_standar_materia-costo_materia-pasarelaCargo_materia-descuento_materia;
      document.getElementById('beneficio_materia').value = "$ " +beneficio_materia.toFixed(2)+"";
    }

    function desc_calc_materia(){
      if(Object.keys(desc_materia).length!=0){
        descuento_materia = desc_materia.cantidad;
        if(desc_materia.tipo == "$"){
          var discount = parseFloat(descuento_materia);
          var preciowdiscount = precio_standar - descuento_materia;
        }
        if(desc_materia.tipo == "%"){
          var discount = parseFloat(precio_standar_materia*parseFloat(descuento_materia/100));
          var preciowdiscount = precio_standar_materia - (precio_standar_materia*parseFloat(descuento_materia/100));
        }
        
        document.getElementById('descuentoField_materia').value = "$ " +discount.toFixed(2)+"";
        document.getElementById('nuevoPrecio_materia').value = "$ " +preciowdiscount.toFixed(2)+"";
        nuevoPrecio_materia = preciowdiscount;
        nuevoPrecioWDescuento_materia = preciowdiscount;
        descuento_materia = discount;
      } 
      else{
        nuevoPrecio_materia = precio_standar_materia;
        document.getElementById('nuevoPrecio_materia').value = "$ " +nuevoPrecio_materia.toFixed(2)+"";
      }
    }

    function desc_ad_calc_materia(){
      if(desc_ad_materia.descuento_adicional!=0 && desc_ad_materia.descuento_adicional!=''){
        descuentoAdicional_materia = desc_ad_materia.descuento_adicional;
        
        if(desc_ad_materia.tipo_descuento == 2){
          var discount = descuentoAdicional_materia;
          var preciowdiscount = beneficio_materia - descuentoAdicional_materia;
        }
        if(desc_ad_materia.tipo_descuento == 1){
          var discount = beneficio_materia*parseFloat(descuentoAdicional_materia/100);
          var preciowdiscount = beneficio_materia - (beneficio_materia*parseFloat(descuentoAdicional_materia/100));
        }
        document.getElementById('descuentoAdicionalField_materia').value = "$ " +parseInt(discount).toFixed(2)+"";
        document.getElementById('nuevoPrecio_materia').value = "$ " +(preciowdiscount+costo_materia).toFixed(2)+"";
        nuevoPrecio_materia = preciowdiscount;
        descuentoAdicional_materia = discount;
      }
      else{
          descuentoAdicional_materia = 0;
          document.getElementById('descuentoAdicionalField_materia').value = "$ " +parseInt(descuentoAdicional_materia).toFixed(2)+"";
          document.getElementById('comision_materia').value = "$ " +comision_materia.toFixed(2);
          document.getElementById('nuevoPrecio_materia').value = "$ " +nuevoPrecio_materia.toFixed(2)+"";
      }
      
    }

    function benef_final_calc_materia(){
      beneficioFinal_materia = beneficio_materia-descuentoAdicional_materia; 
      document.getElementById('beneficioFinal_materia').value = "$ " +(beneficioFinal_materia).toFixed(2)+"";
    }

    function utilidad_calc_materia(){
      utilidad_materia=beneficioFinal_materia-comision_materia;
      // if(utilidad_materia<=500){
      //   utilidad_materia=500;
      //   comision_materia = beneficioFinal_materia-utilidad_materia;
      //   document.getElementById('comision_materia').value = "$ " +comision_materia.toFixed(2);
      // }
      document.getElementById('utilidad_materia').value = "$ " +utilidad_materia.toFixed(2)+"";
    }

    function resetAll_materia(){
      tipo_comision_materia='', beneficio_materia = 0, utilidad_materia = 0,benef_final_materia = 0,precio_standar_materia=0,descuento_materia=0,pasarelaCargo_materia=0,descuentoAdicional_materia=0,nuevoPrecio_materia=0,costo_materia=0,comisionOrg_materia=0,comision_materia=0,beneficioFinal_materia=0,nuevoPrecioWDescuento_materia=0;      matr = {},desc_materia = {},pas_materia = {},desc_ad_materia = {"tipo_descuento":1,'descuento_adicional':0};
      data_calc.matr = {}, data_calc.desc_materia = {},data_calc.pas_materia = {},data_calc.desc_ad_materia = {"tipo_descuento":1,'descuento_adicional':0};
      desc_calc_materia()
      desc_ad_calc_materia()
      benef_calc_materia()
      benef_final_calc_materia()
      utilidad_calc_materia()
      document.getElementById('precioEstandar_materia').value = "$ 00.00";
      document.getElementsByName("discount_materia").forEach(element => element.setAttribute('disabled',''));
      document.getElementsByName("pasarelaChose_materia").forEach(element => element.setAttribute('disabled',''));
      document.getElementById("descuentoAdicional_materia").setAttribute('disabled','');
      document.getElementById("tipoDescuento_materia").setAttribute('disabled','');
      document.getElementById('descuento_materia').setAttribute('disabled','');
      document.getElementsByName("discount_materia").forEach(element => element.setAttribute('disabled',""));
      document.getElementById('comision_materia').value = "$ 00.00";     
      document.getElementById('nuevoPrecio_materia').value = "$ 00.00";
      document.getElementById('costo_materia').value = "$ 00.00";    
    }
    
    function pasarela_calc_materia(){
        if(Object.keys(pas_materia).length!=0){
          document.getElementById('pasarelaComision_materia').value = pas_materia.comision+" %";
          pasarelaCargo_materia = ((nuevoPrecio_materia)*parseFloat(pas_materia.comision/100)+pas_materia.comision_fija)+(((nuevoPrecio_materia)*parseFloat(pas_materia.comision/100)+pas_materia.comision_fija)*(pas_materia.iva/100));
          document.getElementById('pasarelaComisionTotal_materia').value = "$ "+pasarelaCargo_materia.toFixed(2);
          document.getElementById('pasarelaComisionFija_materia').value = "$ "+pas_materia.comision_fija.toFixed(2);
        }
    }
  });
  
  //a
  $(document).on('click','#report-generate', async function(event) {
    const url = '{{asset('assets/docs/ticketCalculadora.pdf')}}';
    const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer());
    const pdfDoc = await PDFDocument.load(existingPdfBytes);
    const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman);
    const page = pdfDoc.getPages()[0];
    const { width, height } = page.getSize();
    const fontSize = 10
    var ts = [
      {n:'empresa',t:(empresaData.nombre != undefined) ? empresaData.nombre : 'Sin Nombre',x:225,y:679,s:12},
      {n:'user',t:'{{auth()->user()->name}}',x:85,y:656,s:12},
      {n:'date',t:"{{date('d/m/Y H:i:s')}}",x:385,y:656,s:12},
      {n:'progname',t:(prod.nombre != undefined) ? prod.nombre : 'Sin Nombre',x:265,y:590,s:12},
      {n:'progprice',t:precio_standar,x:265,y:568,s:12},
      {n:'progdescuento',t:(desc.cantidad != undefined) ? desc.cantidad : '00.00',x:265,y:547,s:12},
      {n:'progdescuentoprice',t:(desc.cantidad != undefined) ? desc.cantidad : '00.00',x:400,y:547,s:12},
      {n:'progpasarela',t:(pas.nombre != undefined) ? pas.nombre : 'Sin Pasarela',x:265,y:527,s:12},
      {n:'progcompas',t:pasarelaCargo,x:265,y:507,s:12},
      {n:'progdescadicional',t:desc_ad.descuento_adicional,x:265,y:484,s:12},
      {n:'progdescadicionalprice',t:descuentoAdicional,x:400,y:484,s:12},
      {n:'progcomision',t:comision,x:265,y:462,s:12},
      {n:'progtotal',t:document.getElementById('nuevoPrecio').value,x:265,y:437,s:12},
    ];

    var tsm = [
      {n:'empresa',t:(empresaData.nombre != undefined) ? empresaData.nombre : 'Sin Nombre',x:225,y:679,s:12},
      {n:'user',t:'{{auth()->user()->name}}',x:85,y:656,s:12},
      {n:'date',t:"{{date('d/m/Y H:i:s')}}",x:385,y:656,s:12},
      {n:'matprice',t:precio_standar_materia,x:265,y:584,s:12},
      {n:'matdescuento',t:(desc_materia.cantidad != undefined) ? desc_materia.cantidad : '00.00',x:265,y:563,s:12},
      {n:'matdescuentoprice',t:(desc_materia.cantidad != undefined) ? desc_materia.cantidad : '00.00',x:400,y:563,s:12},
      {n:'matpasarela',t:(pas_materia.nombre != undefined) ? pas_materia.nombre : 'Sin Pasarela',x:265,y:543,s:12},
      {n:'matdescadicional',t:pasarelaCargo_materia,x:265,y:523,s:12},
      {n:'matdescadicional',t:desc_ad_materia.descuento_adicional,x:265,y:500,s:12},
      {n:'matdescadicionalprice',t:descuentoAdicional_materia,x:400,y:500,s:12},
      {n:'matcomision',t:comision_materia,x:265,y:480,s:12},
      {n:'mattotal',t:document.getElementById('nuevoPrecio_materia').value,x:265,y:457,s:12},
    ];

    if(Object.keys(data_calc.prod).length<5){
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Necesitas llenar por lo menos los datos basicos, para poder generar un ticket!',
        // footer: '<a href="">Why do I have this issue?</a>'
      })
    }
    else{
      Swal.fire({
      title: '¿Seguro que quieres Generar un ticket?',
      showCancelButton: true,
      confirmButtonText: 'Si',
      cancelButtonText: `Cancelar`,
      showLoaderOnConfirm: true,
      preConfirm: (login) => {
        return fetch(`{{route('calculadora.saveData')}}`,
        {
          method: 'POST',
          body: JSON.stringify(data_calc),
          headers: {'Content-Type': 'application/json'}
        })
          .then(response => {
            if (!response.ok) {
              throw new Error(response.statusText)
            }
            return response.json()
          })
          .catch(error => {
            Swal.showValidationMessage(
              `Request failed: ${error}`
            )
          })
      },
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
    }
   
  });

  
</script>
@endsection