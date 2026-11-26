<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="{{ route('home') }}">Casa de Cultura</a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
        <i class="fas fa-bars"></i>
    </button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        {{-- <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..."
                    aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i
                        class="fas fa-search"></i></button>
            </div> --}}
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        {{-- <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="#!">Ajustes</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <li><a class="dropdown-item" href="#!">Cerrar Sesión</a></li>
            </ul>
        </li> --}}
        @guest
            @if (Route::has('login'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
            @endif

            @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
            @endif
        @else
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }}
                </a>

                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <!-- Opción para editar contraseña -->
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editPassword">
                        Cambiar Contraseña
                    </a>

                    <hr class="dropdown-divider" />

                    <!-- Opción para cerrar sesión -->
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        @endguest
    </ul>
</nav>
@php
    // Menú lateral: el grupo de la página abierta queda desplegado y la opción en negritas (clase "active")
    $activo = fn (...$rutas) => request()->routeIs(...$rutas) ? 'active' : '';
    $grupoOrdenPago = request()->routeIs('adminopago', 'concepto');
    $grupoAlumnos = request()->routeIs('alumno', 'inscripcion', 'opagoalumno', 'tarjeton', 'historico');
    $grupoTalleres = request()->routeIs('edificio', 'aula', 'maestro', 'taller');
    $grupoRentas = request()->routeIs('conceptoxrenta', 'espacio', 'renta');
@endphp
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Inicio</div>
                    <a class="nav-link {{ $activo('home') }}" href="{{ route('home') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Inicio
                    </a>
                    <div class="sb-sidenav-menu-heading">Complementos</div>
                    <a class="nav-link {{ $grupoOrdenPago ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                        data-bs-target="#collapseLayouts2" aria-expanded="{{ $grupoOrdenPago ? 'true' : 'false' }}"
                        aria-controls="collapseLayouts2">
                        <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                        Administación de Orden de Pago
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ $grupoOrdenPago ? 'show' : '' }}" id="collapseLayouts2"
                        aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link {{ $activo('adminopago') }}" href="{{ route('adminopago') }}">Fundamento Legal</a>
                            <a class="nav-link {{ $activo('concepto') }}" href="{{ route('concepto') }}">Concepto</a>
                            {{-- <a class="nav-link" href="{{ route('inscripcion') }}">Inscripción</a>
                            <a class="nav-link" href="{{ route('opago') }}">Orden de pago</a> --}}
                        </nav>
                    </div>

                    <div class="sb-sidenav-menu-heading">Registro</div>
                    <a class="nav-link {{ $grupoAlumnos ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                        data-bs-target="#collapseLayouts" aria-expanded="{{ $grupoAlumnos ? 'true' : 'false' }}"
                        aria-controls="collapseLayouts">
                        <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                        Administación de Alumnos
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ $grupoAlumnos ? 'show' : '' }}" id="collapseLayouts"
                        aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link {{ $activo('alumno') }}" href="{{ route('alumno') }}">Registrar alumnos</a>
                            <a class="nav-link {{ $activo('inscripcion') }}" href="{{ route('inscripcion') }}">Inscripción</a>
                            <a class="nav-link {{ $activo('opagoalumno') }}" href="{{ route('opagoalumno') }}">Orden de pago</a>
                            <a class="nav-link {{ $activo('tarjeton') }}" href="{{ route('tarjeton') }}">Reposición de Tarjetón</a>
                            <a class="nav-link {{ $activo('historico') }}" href="{{ route('historico') }}">Historico</a>
                        </nav>
                    </div>
                    <a class="nav-link {{ $grupoTalleres ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                        data-bs-target="#collapseTalleres" aria-expanded="{{ $grupoTalleres ? 'true' : 'false' }}"
                        aria-controls="collapseTalleres">
                        <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                        Administración de Talleres
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ $grupoTalleres ? 'show' : '' }}" id="collapseTalleres"
                        aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link {{ $activo('edificio') }}" href="{{ route('edificio') }}">Edificios</a>
                            <a class="nav-link {{ $activo('aula') }}" href="{{ route('aula') }}">Aulas</a>
                            <a class="nav-link {{ $activo('maestro') }}" href="{{ route('maestro') }}">Maestros</a>
                            <a class="nav-link {{ $activo('taller') }}" href="{{ route('taller') }}">Talleres</a>
                        </nav>
                    </div>
                    <a class="nav-link {{ $grupoRentas ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                        data-bs-target="#collapsePages" aria-expanded="{{ $grupoRentas ? 'true' : 'false' }}"
                        aria-controls="collapsePages">
                        <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                        Administración de Rentas
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ $grupoRentas ? 'show' : '' }}" id="collapsePages"
                        aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                            <a class="nav-link {{ $activo('conceptoxrenta') }}" href="{{ route('conceptoxrenta') }}">Conceptos por renta</a>
                            <a class="nav-link {{ $activo('espacio') }}" href="{{ route('espacio') }}">Espacios</a>
                            <a class="nav-link {{ $activo('renta') }}" href="{{ route('renta') }}">Rentas</a>
                            {{-- <a class="nav-link" href="{{ route('opagociudadano') }}">Orden de pago</a> --}}
                            {{-- <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#pagesCollapseAuth" aria-expanded="false"
                                aria-controls="pagesCollapseAuth">
                                Authentication
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordionPages">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="login.html">Login</a>
                                    <a class="nav-link" href="register.html">Register</a>
                                    <a class="nav-link" href="password.html">Forgot Password</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#pagesCollapseError" aria-expanded="false"
                                aria-controls="pagesCollapseError">
                                Error
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordionPages">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="401.html">401 Page</a>
                                    <a class="nav-link" href="404.html">404 Page</a>
                                    <a class="nav-link" href="500.html">500 Page</a>
                                </nav>
                            </div> --}}
                        </nav>
                    </div>
                    {{-- <div class="sb-sidenav-menu-heading">Addons</div>
                    <a class="nav-link" href="charts.html">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                        Charts
                    </a>
                    <a class="nav-link" href="tables.html">
                        <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                        Tables
                    </a> --}}
                </div>
            </div>
            {{-- <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                Start Bootstrap
            </div> --}}
        </nav>
    </div>
