<div class="modal fade" id="accesoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Datos de acceso</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <div class="modal-body">
          <label>Correo electrónico</label>
          <input type="text" class="form-control" value="{{$c->usuario->email}}">
          <label>Clave de acceso</label>
          <input type="text" class="form-control" value="{{$c->usuario->codigo2}}">
          <label>Acceso compuesto</label>
          <input type="text" class="form-control" value="https://{{$_SERVER['HTTP_HOST']}}/?u={{urlencode($c->usuario->email)}}&p={{urlencode($c->usuario->codigo2)}}">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
    </div>
  </div>
</div>
