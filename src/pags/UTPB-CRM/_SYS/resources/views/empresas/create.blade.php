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
    <div class="card mt-4 mb-4 ">
        <div class="card-body">
            <a href="{{url('/empresas/list')}}" class="btn btn-info m-0">Regresar</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4>Información general</h4>
        </div>
        <div class="card-body">
            <div class="content mt-5">
                <form action="{{url('/empresas/make')}}" method="POST">
                    @csrf
                    <div class="form-row row">
                        <div class="form-group col-md-6">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control" id="name" placeholder="Nombre" required name="name">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="tipo">Slug</label>
                            <input type="text" class="form-control" id="slug" placeholder="Slug" required  name="slug" readonly>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
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