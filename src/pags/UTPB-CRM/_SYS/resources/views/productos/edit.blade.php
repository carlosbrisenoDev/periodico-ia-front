@extends('users.' . Auth::user()->level->alias . '.home')
@section('content')
    @php
        $show = '';
        if (auth()->user()->levels->alias == 'administrador') {
            $show = '';
        } elseif (auth()->user()->levels->alias == 'ventas') {
            $show = 'd-none';
        }
    @endphp
    <div class="content mt-5">
        <form action="{{url('/productos/update')}}" method="POST">
            @csrf
            <input type="hidden" name="prod_id" value="{{$producto->id}}">
            <div class="form-row row">
                <div class="form-group col-md-6">
                    <label for="name">Nombre</label>
                    <input type="text" class="form-control" id="name" placeholder="Nombre" required name="name" value="{{$producto->nombre}}">
                </div>

                <div class="form-group col-md-6">
                    <label for="tipo">Tipo</label>
                    <input type="text" class="form-control" id="tipo" placeholder="Tipo" required  name="tipo" value="{{$producto->tipo}}">
                </div>
            </div>
            
            <div class="form-row row">
                <div class="form-group col-md-6">
                    <label for="precio">Precio</label>
                    <input type="number" class="form-control" id="precio" placeholder="Tipo" required  name="precio" value="{{$producto->precio}}">
                </div>
                <div class="form-group col-md-6">
                    <label for="costo">Costo</label>
                    <input type="number" class="form-control" id="costo" placeholder="Costo" required  name="costo" value="{{$producto->costo}}">
                </div>
            </div>
            
            <div class="form-row row">
                <div class="form-group col-md-6">
                    <label for="desc_max">Descuento Maximo</label>
                    <input type="number" class="form-control" id="desc_max" placeholder="Descuento Maximo" required  name="desc_max" value="{{$producto->descuento_max}}">
                </div>
                <div class="form-group col-md-6">
                    <label for="tipoDescuento">Tipo de Descuento</label>
                    <select class="form-control" id="tipoDescuento" required  name="tipoDescuento">
                        <option value="$" <?php echo ($producto->tipo_descuento == '$') ? 'selected' : ''; ?>>$</option>
                        <option value="%" <?php echo ($producto->tipo_descuento == '%') ? 'selected' : ''; ?>>%</option>
                    </select>
                </div>
            </div>

            <div class="form-row row" >
                <div class="form-group col-md-6">
                    <label for="comision">Comisión</label>
                    <input type="number" class="form-control" id="comision" placeholder="Comision" required  name="comision" value="{{$producto->comision}}">
                </div>
                <div class="form-group col-md-6">
                    <label for="costdurationMatMat">Duración (meses)/Cantidad Materias</label>
                    <input type="number" class="form-control" id="durationMat" placeholder="Duración/Cantidad Materias" required  name="durationMat"  value="{{$producto->mensualidades}}">
                </div>
            </div>

            <div class="form-row row">
                <div class="form-group col-md-6">
                    <label for="precioMat">Precio por mensualidad/materia</label>
                    <input type="number" class="form-control" id="precioMat" placeholder="Precio por materia/mes" required  name="precioMat" value="{{$producto->precio_materia}}">
                </div>
                <div class="form-group col-md-6">
                    <label for="costoMat">Costo por mensualidad/materia</label>
                    <input type="number" class="form-control" id="costoMat" placeholder="Costo por materia/mes" required  name="costoMat" value="{{$producto->costo_materia}}">
                </div>
            </div>
            

            <div class="form-row row">
                <div class="form-group col-md-6">
                    <label for="descMaxMat">Descuento Maximo Materias/Mensualidad</label>
                    <input type="number" class="form-control" id="descMaxMat" placeholder="Descuento Maximo Materias" required  name="descMaxMat" value="{{$producto->descuento_max_materia}}">
                </div>
                <div class="form-group col-md-6">
                    <label for="tipoDescuentoMat">Tipo de Descuento Max Materia/Mensualidad</label>
                    <select class="form-control" id="tipoDescuentoMat" required  name="tipoDescuentoMat">
                        <option value="$" <?php echo ($producto->tipo_descuento == '$') ? 'selected' : ''; ?>>$</option>
                        <option value="%" <?php echo ($producto->tipo_descuento == '%') ? 'selected' : ''; ?>>%</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="comisionMat">Comisión Materias/Mensualidad</label>
                    <input type="number" class="form-control" id="comisionMat" placeholder="Comisión Maximo Materias" required  name="comisionMat" value="{{$producto->comision_materia}}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
@endsection
