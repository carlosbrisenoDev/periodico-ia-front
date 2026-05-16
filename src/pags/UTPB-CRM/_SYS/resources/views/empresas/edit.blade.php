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
    <div class="card">
        <div class="card-body">
            <a href="{{url('/empresas/list')}}" class="btn btn-info m-0">Regresar</a>
        </div>
    </div>
    <div class="content mt-5">
        <div class="card mb-4 mt-4">
            <div class="card-header">
                <h4>Información general</h4>
            </div>
            <div class="card-body">
                <form action="{{url('/empresas/update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="empr_id" value="{{$empresa->id}}">
                    <div class="form-row row">
                        <div class="form-group col-md-6">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control" id="name" placeholder="Nombre" required name="name" value="{{$empresa->nombre}}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="tipo">Slug</label>
                            <input type="text" class="form-control" id="slug" placeholder="Slug" required  name="slug" readonly value="{{$empresa->slug}}">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h4>Empresa y sus productos</h4>
            </div>
            <div class="card-body">
                <form action="{{url('/empresas/empresas_productos')}}" method="POST">
                    <input type="hidden" name="empr_id" value="{{$empresa->id}}">
                    <div class="row col-12">
                        <div class="col-5">
                            <label for="multiselect" class="h5">Productos generales</label>
                            <select name="out[]" id="multiselect" class="form-control" size="8" multiple="multiple">
                                
                                @foreach($empresa->nonProductos() as $producto)
                                    <option value="{{$producto->id}}">{{$producto->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-1">
                            <button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="fa fa-forward"></i></button>
                            <button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="fa fa-chevron-right"></i></button>
                            <button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="fa fa-chevron-left"></i></button>
                            <button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="fa fa-backward"></i></button>
                        </div>
                        
                        <div class="col-5">
                            <label for="multiselect_to" class="h5">Productos dentro de la empresa</label>
                            <select name="in[]" id="multiselect_to" class="form-control" size="8" multiple="multiple">
                                @foreach($empresa->productos() as $producto)
                                    <option value="{{$producto->id}}">{{$producto->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-info">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="https://cdn.rawgit.com/crlcu/multiselect/master/dist/js/multiselect.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#multiselect').multiselect();
    });
    </script>
<script>
    function string_to_slug (str) {
        str = str.replace(/^\s+|\s+$/g, ''); // trim
        str = str.toLowerCase();
    
        // remove accents, swap ñ for n, etc
        var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
        var to   = "aaaaeeeeiiiioooouuuunc------";
        for (var i=0, l=from.length ; i<l ; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes

        return str;
    }
    document.getElementById('name').addEventListener('keyup', function() {
        $('#slug').val(string_to_slug(this.value));
        console.log()
    });
//   console.log(string_to_slug('Hola como estas'));
</script>
@endsection