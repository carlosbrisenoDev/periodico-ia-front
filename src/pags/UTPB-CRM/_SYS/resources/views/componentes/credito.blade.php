<div class="modal fade" id="creditoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Crédito</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="" action="/clientes/credito" method="post">
        <div class="modal-body">
          <input type="hidden" name="cid" value="{{md5($c->id)}}">
          <label for="">Interés del crédito</label>
          <select class="form-control" name="credito">
            @for($i = 0 ;$i < 13; $i++)
              <option {{$c->credito == $i ? "selected" : ""}} value="{{$i}}">{{$i}}%</option>
            @endfor
            <option {{$c->credito == null ? "selected" : ""}} value="null">Desactivar crédito</option>
          </select>
          <label for="">Plazo del crédito</label>
          <select class="form-control" name="plazo">
            @for($i = 1 ;$i < 49; $i++)
              <option {{$c->plazo == $i ? "selected" : ""}} value="{{$i}}">{{$i}} mes{{$i>1?"es":""}}</option>
            @endfor
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
