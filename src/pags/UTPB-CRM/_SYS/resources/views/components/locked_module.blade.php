@php
    $lockEmail = env('LOCK_CONTACT_EMAIL', 'ventas@utbp.edu.mx');
    $lockPhone = env('LOCK_CONTACT_PHONE', '+5210000000000');
    $module = $moduleName ?? 'Este módulo';
    $telHref = preg_replace('/\s+/', '', $lockPhone);
@endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-4">
                    <h3 class="mb-2"><i class="fa fa-lock"></i> {{ $module }} bloqueado</h3>
                    <p class="text-muted mb-4">
                        Este módulo no está incluido en tu edición actual de UTBP CRM.
                    </p>
                    <a class="btn btn-primary me-2" href="mailto:{{ $lockEmail }}">
                        <i class="fa fa-envelope"></i> Contactar por correo
                    </a>
                    <a class="btn btn-outline-primary" href="tel:{{ $telHref }}">
                        <i class="fa fa-phone"></i> Contactar por teléfono
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
