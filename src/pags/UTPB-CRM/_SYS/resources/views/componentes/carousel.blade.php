<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
  <div class="carousel-inner">
    @for ($i=1; $i <= 2; $i++)
      <div class="carousel-item {{($i == 1) ? "active" : ""}}">
        <img class="d-block w-100" src="{{ asset('images/promos/promos'.$i.'.jpg')}}" alt="First slide">
      </div>
    @endfor
  </div>
  <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
