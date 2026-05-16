<div class="modal fade" id="comprobanteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Comprobante</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="" action="/clientes/comprobante" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="cid" value="{{md5($c->id)}}">
          <label for="">Selecciona el comprobante de pago:</label>
          <input type="file" name="file">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
