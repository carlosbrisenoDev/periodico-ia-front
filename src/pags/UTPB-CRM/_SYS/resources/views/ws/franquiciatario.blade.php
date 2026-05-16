@extends('layouts.website')
@section('content')
  <div class="row">
    <div class="col-12 col-md-6">
      <div class="heading">
        <h3>
        </h3>
        <div class="h">

        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <img src="{{ asset('images/franquicia.jpg')}}" class="img">
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <p class="text gray" align="justify">
        Una prominente marca con más de 25 años de trayectoria regida bajo un modelo de negocio que le asegura a nuestros inversionistas: éxito, seguridad y prosperidad para usted y su familia.
      </p>
      <div class="card borderless">
        <div class="card-body">
          <form class="" action="/franquiciatarios/registro" method="post">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail4">Nombre completo</label>
              <input type="text" class="form-control" name="nombre" required id="inputEmail4" placeholder="Nombre completo">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail4">Correo electrónico</label>
              <input type="email" class="form-control" name="correo" required id="inputEmail4" placeholder="Correo electrónico">
            </div>
          </div>
          <div class="form-group">
            <label for="inputAddress">Dirección</label>
            <input type="text" class="form-control" name="direccion" required id="inputAddress" placeholder="Dirección">
          </div>
          <div class="form-group">
            <label for="inputAddress2">Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="inputAddress2" placeholder="(555) 228 00 00)">
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputCity">Ciudad</label>
              <input type="text" class="form-control" required  placeholder="Ciudad" id="inputCity">
            </div>
          </div>
          <div class="card">
            <div class="card-body">
              <div class="form-group">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="gridCheck" required>
                  <label class="form-check-label" for="gridCheck">
                    He leído el <a href="/shirushi/avisodeprivacidad">aviso de privacidad</a> y quiero que me envien un correo electrónico con más información.
                  </label>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Solicitar información</button>
            </div>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
@endsection
