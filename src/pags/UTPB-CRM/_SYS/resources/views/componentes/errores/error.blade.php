<div aria-live="polite" aria-atomic="true" style="position: relative; min-height: 200px;">
    <div style="position: fixed; bottom: 15px; right: 15px;" class="pops">
      @if (session('status'))
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000">
          <div class="toast-header">
              <div class="float-start">
                <small class="text-muted">Justo ahora</small>
              </div>
          </div>
          <div class="toast-body">
            {{session('status')}}
          </div>
        </div>
      @endif
    </div>
  </div>