@if ($beca && $pago->status == NULL)
  <form action="/cartera/beca" enctype="multipart/form-data" method="post">
        <div class="clearfix">
          <input type="hidden" name="cid" value="{{md5($pago->id)}}">
          <div class="float-left">
          </div>
          <div class="float-right">
            <input type="submit" class="btn btn-outline-primary btn-sm" name="" value="Marcar como pagado, Beca.">
          </div>
        </div>
  </form>
@elseif ($beca && $pago->status == 9)
  <div class="clearfix">
    <div class="float-left">
      Becado <i class="fas fa-check-circle" style="color:green;"></i>
    </div>
  </div>
@elseif($pago->pagado == 1)
  Subir comprobante
  <form action="/cartera/comprobante" enctype="multipart/form-data" method="post">
        <div class="clearfix">
          <input type="hidden" name="cid" value="{{md5($pago->id)}}">
          <div class="float-left">
            <input type="file" class="comprobante" name="file" value="">
          </div>
          <div class="float-right">
            <input type="submit" class="btn btn-outline-primary btn-sm" name="" value="Subir comprobante de pago">
          </div>
        </div>
  </form>
@elseif ($pago->pagado != null && $pago->status == 0)
  <div class="clearfix">
    <input type="hidden" name="cid" value="{{md5($pago->id)}}">
    <div class="float-left">
      Comprobante:
      <ul>
        <li>
          <div class="extrapay" data-bs-toggle="modal" data-bs-target="#extrapay" cid="{{md5($pago->id)}}">
            <a href="#">
              <i class="far fa-money-bill-alt"></i> Pago extra
            </a>
          </div>
        </li>
        <li><a target="_blank" href="/documentos/watchar/{{md5($pago->pagado)}}"><i class="fas fa-image"></i> Ver
        </a></li>
        <li>
        <a href="/documentos/download/{{md5($pago->pagado)}}"><i class="fas fa-download"></i> Descargar</a></li>
        <li>
        <a href="/cartera/eliminar/document?cid={{md5($pago->id)}}"><i class="fas fa-trash"></i> Eliminar comprobante
        </a></li>
      </ul>
    </div>
    <div class="float-right">
      <a href="/cartera/pagar/document?cid={{md5($pago->id)}}" class="btn btn-outline-primary btn-sm">Marcar como pagado</a>
    </div>
  </div>
@elseif($pago->pagado == null)
    Subir comprobante
    <form action="/cartera/comprobante" enctype="multipart/form-data" method="post">
          <div class="clearfix">
            <input type="hidden" name="cid" value="{{md5($pago->id)}}">
            <div class="float-left">
              <input type="file" class="comprobante" name="file" value="">
            </div>
            <div class="float-right">
              <input type="submit" class="btn btn-outline-primary btn-sm" name="" value="Subir comprobante de pago">
            </div>
          </div>
    </form>
  @else
    <div class="clearfix">
      <div class="float-left">
        Pagado <i class="fas fa-check-circle" style="color:green;"></i>
      </div>
      <div class="float-right">
        <a href="/cartera/unpagar/document?cid={{md5($pago->id)}}">Verificar pago</a>
      </div>
    </div>
@endif
