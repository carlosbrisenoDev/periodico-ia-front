<div class="row">
  <div class="col-12" id="drop">
    <div class="card">
      <div class="card-body">
        <form action="/documentos/subirventas?id={{$c->id}}" class="dropzone" id="dropzone">
          <div class="fallback">
            <input name="file" type="file" multiple />
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
