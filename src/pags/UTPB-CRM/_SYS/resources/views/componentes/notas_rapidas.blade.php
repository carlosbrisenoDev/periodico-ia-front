@php
$ctag = \App\cliente_tag::where("cliente_id",$c->id)->orderBy('created_at','DESC')->get();
$arr_col = array(1=>'success',2=>'warning',3=>'danger');
@endphp
<br>
<div class="row">
    <div class="card-body">
        <h5 class="card-title">Notas Rapidas</h5>
        {{-- <span class="badge bg-danger"><strong>Esta sección aun no esta disponible para su uso.</strong></span> --}}
        @if(count($ctag)>0)
            {{-- <span class="badge bg-success">Nota rapida de ejemplo 1</span>
            <span class="badge bg-danger">Nota rapida de ejemplo 2</span>
            <span class="badge bg-warning">Nota rapida de ejemplo 3</span> --}}
            @foreach($ctag as $tag)
                <span class="badge bg-{{$arr_col[$tag->nivel_nota]}}">{{$tag->nota}}
                    <form action="/ventas/remove_cliente_tag" method="post" style="display: unset;">
                        <input type="hidden" name="tag_id" value="{{md5($tag->id)}}">
                        <input type="hidden" name="cliente_id" value="{{md5($c->id)}}">
                        <input type="submit" value="X" style="background: none;border: none;color: white;">
                    </form>
            </span>
            @endforeach
        @else
            <span class="badge bg-secondary">Sin notas</span>
        @endif
        <br>
        <br>
        <div class="row">
            <form action="/ventas/cliente_tag" method="post">
                <input type="hidden" name="cliente_id" value="{{$c->id}}">
                <div class="col-12">
                <textarea name="nota" class="form-control" placeholder="Agregar nota ..." id="notarapidatextarea"></textarea>
                <br>
                @php
                    $notas = collect();
                    $queryNotes = \App\notas_cliente::where('usuario_id',auth()->user()->id)->limit(10)->get();
                    // dd($queryNotes);
                @endphp
                <div class="table-responsive">
                    <small>Sugerencias rapidas, da click a una para completar el campo.</small>
                    <br>
                    @foreach($queryNotes as $queryNota)
                        <a class="badge bg-secondary cursor-pointer quicknote text-white" data-note="{{$queryNota->nota}}">{{$queryNota->nota}}</a>
                    @endforeach
                    <br>
                </div>
                <br>
                {{-- <div class="form-group">
                    <label for="exampleFormControlSelect1">Elije un estado para la nota</label>
                    <select class="form-control selectpicker" data-style="btn btn-link" id="exampleFormControlSelect1">
                      <option value="1" style="background:#82d616;">1 (Verde)</option>
                      <option value="2" style="background:#fbcf33;">2 (Amarillo)</option>
                      <option value="3" style="background:#ea0606;">3 (Rojo)</option>
                    </select>
                </div> --}}
                    <div class="d-flex">
                        <div class="form-check form-check-radio col-4">
                            <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="nivel_nota" id="nivel_nota1" value="1" checked style="background-image: linear-gradient(310deg, #82d616 0%, #82d616 100%);">
                                Probable
                                <span class="circle">
                                    <span class="check"></span>
                                </span>
                            </label>
                        </div>
                        <div class="form-check form-check-radio col-4">
                            <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="nivel_nota" id="nivel_nota2" value="2" style="background-image: linear-gradient(310deg, #fbcf33 0%, #fbcf33 100%);">
                                Poco Probable
                                <span class="circle">
                                    <span class="check"></span>
                                </span>
                            </label>
                        </div>
                        <div class="form-check form-check-radio col-4">
                            <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="nivel_nota" id="nivel_nota3" value="3" style="background-image: linear-gradient(310deg, #ea0606 0%, #ea0606 100%);">
                                Nada de Interes
                                <span class="circle">
                                    <span class="check"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="" title="">
                    <i class="fas fa-comment-alt" aria-hidden="true"></i> Guardar Nota Rapida
                </button>
                </div>
            </form>
        </div>
    </div>
    
</div>
<br>