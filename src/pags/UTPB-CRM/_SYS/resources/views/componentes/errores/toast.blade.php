@if (session('error'))
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000">
              <div class="toast-header">
                <i class="fa fa-info-circle"></i>
                <strong class="mr-auto" style="padding-left:5px;"> SII</strong>
                <small class="text-muted">Justo ahora</small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="toast-body">
                {{ session('error') }}
              </div>
            </div>
        @endif