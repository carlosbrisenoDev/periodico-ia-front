{{-- INICIO --}}
    <li class="nav-item ms-lg-auto" style="padding: 10px;">
        <a class="nav-link nav-link-icon me-2" href="{{url('/')}}">
        <i class="fa fa-home text-police-blue"></i>
        <p class="d-inline text-sm z-index-1 font-weight-bold" data-toggle="tooltip" data-placement="bottom">Inicio</p>
        </a>
    </li>
{{-- FIN INICIO --}}

{{-- INSTALL --}}
{{-- <li class="nav-item ms-lg-auto menu-button-pwa" style="padding: 10px;display:none;">
    <a class=" nav-link nav-link-icon me-2 add-button-pwa cursor-pointer">
    <i class="fa fa-download text-police-blue" aria-hidden="true"></i>
    <p class="d-inline text-sm z-index-1 font-weight-bold" data-toggle="tooltip" data-placement="bottom">Instalar</p>
    </a>
</li> --}}

{{-- FIN INSTALL --}}

@php
    $blockedAliases = ['cliente', 'comunicacion', 'direccion', 'franquiciatario', 'marketing', 'observador', 'papeleria', 'relacionespublicas', 'rh'];
    $currentAlias = auth()->user()->levels->alias;
    $isBlockedAlias = in_array($currentAlias, $blockedAliases, true);
@endphp

@if(!$isBlockedAlias && (auth()->user()->levels->alias=='administrador' || (auth()->user()->email=='egarcia@unisantorizaba.com' || auth()->user()->email=='egarcia@unisant.edu.mx')))
    {{-- USUARIOS --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-user text-police-blue"></i>
                <span style="margin-left: 2px;">Usuarios</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages" style="left: -60%;">
                <div class=" d-lg-block">
                    <a href="/{{ Auth::user()->level->alias }}/nuevo" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Nuevo</span>
                    </a>
                    <a href="/{{ Auth::user()->level->alias }}/buscar" class="dropdown-item border-radius-md">
                        <i class="fa fa-search text-police-blue"></i><span> Buscar</span>
                    </a>
                    <a href="/{{ Auth::user()->level->alias }}/sedes" class="dropdown-item border-radius-md">
                        <i class="fa fa-list text-police-blue"></i><span> Sedes</span>
                    </a>
                    <a href="/{{ Auth::user()->level->alias }}/areas" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Áreas</span>
                    </a>
                    <a href="/{{ Auth::user()->level->alias }}/reportesarea" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Reporte por área</span>
                    </a>
                    
                </div>
            </div>
        </li>
    {{-- FIN USUARIOS --}}

    {{-- LINEAS --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-phone text-police-blue"></i>
                <span style="margin-left: 2px;">Lineas</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages" style="left: -60%;">
                <div class=" d-lg-block">
                    <a href="/{{ Auth::user()->level->alias }}/lineas" class="dropdown-item border-radius-md">
                        <i class="fa fa-list text-police-blue"></i><span> Lineas</span>
                    </a>
                    <a href="/{{ Auth::user()->level->alias }}/linea_nuevo" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Nueva</span>
                    </a>
                    
                </div>
            </div>
        </li>
    {{-- FIN LINEAS --}}

    {{-- FRANQUICIAS --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-phone text-police-blue"></i>
                <span style="margin-left: 2px;">Franquicias</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages">
                <div class=" d-lg-block">
                    <a href="/franquiciatarios/solicitantes/lista" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Solicitantes</span>
                    </a>
                    <a href="/franquiciatarios/lista/franquicias" class="dropdown-item border-radius-md">
                        <i class="fa fa-list text-police-blue"></i><span> Lista de franquicias</span>
                    </a>
                    
                </div>
            </div>
        </li>
    {{-- FIN FRANQUICIAS --}}

    {{-- PRODUCTOS UTBP --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-paperclip text-police-blue"></i>
                <span style="margin-left: 2px;">Productos UTBP</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages" style="left: -60%;">
                <div class=" d-lg-block">
                    <a href="{{url('/productos/list')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-book text-police-blue"></i><span> Productos</span>
                    </a>
                    <a href="{{url('/productos/crear')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Crear Productos</span>
                    </a>
                    <a href="{{url('/empresas/list')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-book text-police-blue"></i><span> Empresas</span>
                    </a>
                    <a href="{{url('/empresas/crear')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Crear Empresas</span>
                    </a>
                    
                </div>
            </div>
        </li>
    {{-- FIN PRODUCTOS UTBP --}}

    {{-- METAS --}}
        <li class="nav-item ms-lg-auto" style="padding: 10px ;">
            <a class="nav-link nav-link-icon me-2" href="/metas">
            <i class="fa fa-flag-checkered text-police-blue" aria-hidden="true"></i>
            <p class="d-inline text-sm z-index-1 font-weight-bold" data-toggle="tooltip" data-placement="bottom">Metas</p>
            </a>
        </li>
    {{-- FIN METAS --}}


@elseif(auth()->user()->levels->alias=='ventas')
    {{--  CLIENTES --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-users text-police-blue"></i>
                <span style="margin-left: 4px;"> Clientes</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages" style="left: -60%;">
                <div class=" d-lg-block">
                    <a href="/{{Auth::user()->level->alias}}/nuevo" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Nuevo Cliente</span>
                    </a>
                    <a href="/{{Auth::user()->level->alias}}/listar" class="dropdown-item border-radius-md">
                        <i class="fa fa-list text-police-blue"></i><span> Todos los clientes</span>
                    </a>
                    
                </div>
            </div>
        </li>
    {{-- FIN CLIENTES --}}
    
    {{-- sad --}}
@elseif(auth()->user()->levels->alias=='creditos')
    {{--  CLIENTES --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-users text-police-blue"></i>
                <span style="margin-left: 4px;"> Clientes</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages" style="left: -60%;">
                <div class=" d-lg-block">
                    <a href="/{{Auth::user()->level->alias}}/notify" class="dropdown-item border-radius-md">
                        <i class="fa fa-file-invoice-dollar"></i><span> Pagos atrasados</span>
                    </a>
                    <a href="/{{Auth::user()->level->alias}}/noventa" class="dropdown-item border-radius-md">
                        <i class="fa fa-exclamation-circle text-danger"></i><span> 60 Días o más</span>
                    </a>
                    <a href="/{{Auth::user()->level->alias}}/pagos" class="dropdown-item border-radius-md">
                        <i class="fa fa-file-invoice"></i><span> Pagos {{Date("M")}}</span>
                    </a>
                    <a href="/{{Auth::user()->level->alias}}/listar" class="dropdown-item border-radius-md">
                        <i class="fa fa-users"></i><span> Clientes</span>
                    </a>
                    
                </div>
            </div>
        </li>
    {{-- FIN CLIENTES --}}

    {{--  MATERIAS --}}
        {{-- <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-book text-police-blue"></i>
                <span style="margin-left: 4px;"> Materias</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages">
                <div class=" d-lg-block">
                    <a href="/{{Auth::user()->level->alias}}/materias" class="dropdown-item border-radius-md">
                        <i class="fa fa-cubes text-police-blue"></i><span> Materias en curso</span>
                    </a>
                    
                </div>
            </div>
        </li> --}}
    {{-- FIN MATERIAS --}}
@elseif(auth()->user()->levels->alias=='controlescolar')

    {{--  CLIENTES --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-users text-police-blue"></i>
                <span style="margin-left: 4px;"> Clientes</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages" style="left: -60%;">
                <div class=" d-lg-block">
                    <a  href="/ventas/nuevo" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Nuevo Cliente</span>
                    </a>
                    <a  href="/ventas/listar" class="dropdown-item border-radius-md">
                        <i class="fa fa-list text-police-blue"></i><span> Todos los clientes</span>
                    </a>
                    
                </div>
            </div>
        </li>
    {{-- FIN CLIENTES --}}

    {{--  MARKETING --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-shopping-cart text-police-blue"></i>
                <span style="margin-left: 4px;"> Ventas</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages">
                <div class=" d-lg-block">
                    <a href="/ventas/gacetadeenvio" class="dropdown-item border-radius-md">
                        <i class="fa fa-envelope text-police-blue"></i><span> iBrochures</span>
                    </a>
                    
                </div>
            </div>
        </li>
    {{-- FIN MARKETING --}}

    {{--  CONTROL ESCOLAR --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-chalkboard-teacher text-police-blue"></i>
                <span style="margin-left: 4px;"> Control escolar</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages" style="left: -60%;">
                <div class=" d-lg-block">
                    <a href="/ventas/alumnos" class="dropdown-item border-radius-md">
                        <i class="fa fa-file text-police-blue"></i><span> Alumnos ({{\Carbon\carbon::now()->format("M Y")}})</span>
                    </a>
                    <a href="/ventas/calendario" class="dropdown-item border-radius-md">
                        <i class="fa fa-calendar text-police-blue"></i><span> Cal ({{\Carbon\carbon::now()->format("M Y")}})</span>
                    </a>
                    <a href="/ventas/facturas" class="dropdown-item border-radius-md">
                        <i class="fa fa-file-invoice text-police-blue"></i><span> Facturación</span>
                    </a>
                    <a href="/ventas/notify" class="dropdown-item border-radius-md">
                        <i class="fa fa-flag text-police-blue"></i><span> Notificar alumnos</span>
                    </a>
                    
                    {{-- <a href="/controlescolar/upload" class="dropdown-item border-radius-md">
                        <i class="fa fa-upload text-police-blue"></i><span> Subir materias</span>
                    </a> --}}
                    
                </div>
            </div>
        </li>
    {{-- FIN  CONTROL ESCOLAR --}}

    {{--  MATERIAS --}}
        {{-- <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-book text-police-blue"></i>
                <span style="margin-left: 4px;"> Materias</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages">
                <div class=" d-lg-block">
                    <a href="/creditos/materias" class="dropdown-item border-radius-md">
                        <i class="fa fa-cubes text-police-blue"></i><span> Materias en curso</span>
                    </a>
                    
                </div>
            </div>
        </li> --}}
    {{-- FIN MATERIAS --}}

    {{-- PRODUCTOS UTBP --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-paperclip text-police-blue"></i>
                <span style="margin-left: 2px;">Productos UTBP</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages" style="left: -60%;">
                <div class=" d-lg-block">
                    <a href="{{url('/productos/list')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-book text-police-blue"></i><span> Productos</span>
                    </a>
                    <a href="{{url('/productos/crear')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Crear Productos</span>
                    </a>
                    <a href="{{url('/empresas/list')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-book text-police-blue"></i><span> Empresas</span>
                    </a>
                    <a href="{{url('/empresas/crear')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Crear Empresas</span>
                    </a>
                </div>
            </div>
        </li>
    {{-- FIN PRODUCTOS UTBP --}}

    {{--  MATERIAS --}}
        {{-- <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-headset text-police-blue"></i>
                <span style="margin-left: 4px;"> Call Center</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages" style="left: -60%;">
                <div class=" d-lg-block">
                    <a href="/ventas/rollcenter" class="dropdown-item border-radius-md">
                        <i class="fa fa-phone-volume text-police-blue"></i><span> Llamar clientes</span>
                    </a>
                    <a href="/ventas/listarcenter" class="dropdown-item border-radius-md">
                        <i class="fa fa-address-book text-police-blue"></i><span> Todos los clientes</span>
                    </a>
                    
                </div>
            </div>
        </li> --}}
    {{-- FIN MATERIAS --}}

@elseif(auth()->user()->levels->alias=='alumnos')
    @if (Auth::user()->empleado)
        @if(Auth::user()->empleado->status == 5)
        <li>
            <a href="/home">
                <i class="fas fa-newspaper"></i> Noticias
            </a>
        </li>
        @endif
    @endif
@endif

@if(!$isBlockedAlias && auth()->user()->levels->alias!='alumnos')
    {{-- TICKETS --}}
        <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
            <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-paperclip text-police-blue"></i>
                <span style="margin-left: 2px;">Reportes</span>
                <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
            </a>
            <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
                aria-labelledby="dropdownMenuPages" style="left: -60%;">
                <div class=" d-lg-block">
                    <a href="{{url('/reporte/mylist')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-book text-police-blue"></i><span> Mis Reportes Creados</span>
                    </a>
                    <a href="{{url('/reporte/list')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-book text-police-blue"></i><span> Reportes Pendientes</span>
                    </a>
                    <a href="{{url('/reporte/crear')}}" class="dropdown-item border-radius-md">
                        <i class="fa fa-plus text-police-blue"></i><span> Crear Nuevo Reporte</span>
                    </a>
                    
                </div>
            </div>
        </li>
    {{-- TICKETS --}}
@endif

@if(!$isBlockedAlias)
{{-- Actividades --}}
    <li class="nav-item dropdown dropdown-hover mx-2 cursor-pointer">
        <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-table text-police-blue"></i>
            <span style="margin-left: 4px;"> Actividades</span>
            <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
        </a>
        <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
            aria-labelledby="dropdownMenuPages" style="left: -60%;">
            <div class=" d-lg-block">
                <a href="/actividades/create" class="dropdown-item border-radius-md">
                    <i class="fa fa-plus text-police-blue"></i><span> Nuevo Registro de Actividad</span>
                </a>
                <a href="/actividades/list" class="dropdown-item border-radius-md">
                    <i class="fa fa-list text-police-blue"></i><span> Ver mis Actividades Realizadas</span>
                </a>
                @if(auth()->user()->levels->alias=='administrador' || auth()->user()->id==932 || auth()->user()->id==428 || auth()->user()->id==631)
                <a href="/actividadesCatalogo/list" class="dropdown-item border-radius-md">
                    <i class="fa fa-list text-police-blue"></i><span> Ver Catalogo de Actividades</span>
                </a>
                <a href="/actividadesCatalogo/create" class="dropdown-item border-radius-md">
                    <i class="fa fa-plus text-police-blue"></i><span> Crear Nueva Actividad</span>
                </a>
                <a href="/actividades/global/list" class="dropdown-item border-radius-md">
                    <i class="fa fa-list text-police-blue"></i><span> Ver Registro Global</span>
                </a>
                @endif
            </div>
        </div>
    </li>
{{-- fin actividades --}}
@endif

{{-- CUENTA --}}
    <li class="nav-item dropdown dropdown-hover cursor-pointer mx-2">
        <a class="nav-link ps-2 d-flex cursor-pointer align-items-center" id="dropdownMenuPages" data-toggle="dropdown" aria-expanded="false" >
            <i class="fa fa-user text-police-blue"></i>
            <span style="margin-left: 2px;">{{auth()->user()->name}} (<small><i class="fa-solid fa-address-book"></i> {{auth()->user()->levels->name}}</small>)</span>
            <img src="{{asset('/assets/img/down-arrow-dark.svg')}}" alt="down-arrow" class="arrow ms-auto ms-md-2">
        </a>
        <div class="dropdown-menu dropdown-menu-animation ms-n3 dropdown-md p-3 border-radius-xl mt-0 mt-lg-3"
            aria-labelledby="dropdownMenuPages" style="left: -10%;">
            <div class=" d-lg-block">
                @if(auth()->user()->ccuser && auth()->user()->ccpassword)

                <button class="dropdown-item border-radius-md cursor-pointer" target="_blank" onClick="window.open('https://edav.my.connect.aws/login','CallCenter ECM','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,height=750px,width=450px'); return false;">
                {{-- <button class="dropdown-item border-radius-md cursor-pointer" data-toggle="modal" data-target="#myModalCC"> --}}
                    <i class="fa fa-headset text-police-blue" aria-hidden="true"></i>
                    <p class="d-inline text-sm z-index-1 font-weight-bold" data-toggle="tooltip" data-placement="bottom">Call Center</p>
                </button>
                @endif
                <a class="dropdown-item border-radius-md cursor-pointer" target="_blank" href="{{url('/')}}/omnichannel">
                    <i class="fa-brands fa-whatsapp text-police-blue" aria-hidden="true"></i>
                    <p class="d-inline text-sm z-index-1 font-weight-bold" data-toggle="tooltip" data-placement="bottom">Canal único</p>
                </a>
                <a class="dropdown-item border-radius-md add-button-pwa cursor-pointer">
                    <i class="fa fa-download text-police-blue" aria-hidden="true"></i>
                    <p class="d-inline text-sm z-index-1 font-weight-bold" data-toggle="tooltip" data-placement="bottom">Instalar</p>
                </a>
                <a class="dropdown-item border-radius-md" href="{{ url('/edit/info/'.md5(auth()->user()->id)) }}">
                    <i class="fa fa-user text-police-blue" aria-hidden="true"></i>
                    <p class="d-inline text-sm z-index-1 font-weight-bold" data-toggle="tooltip" data-placement="bottom">Mi Información</p>
                </a>
                <form action="{{url('/logout')}}" method="POST" >
                    @csrf
                    <button type="submit" class="dropdown-item border-radius-md">
                        <i class="fa fa-ban text-police-blue"></i><span> Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </li>
{{-- FIN CUENTA --}}

{{-- USER --}}

{{-- <li class="nav-item ms-lg-auto">
    
    <a class="nav-link nav-link-icon me-2" href="https://github.com/creativetimofficial/material-kit" target="_blank">
    <i class="fa fa-user me-1" aria-hidden="true"></i>
    <p class="d-inline text-sm z-index-1 font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="bottom"></p>
    </a>
    
</li> --}}

{{-- FIN USER --}}
