            <div class="app-sidebar-menu">
                <div class="h-100" data-simplebar>

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">

                        <div class="logo-box">
                            <a href="{{ url('/') }}" class="logo logo-light liliwmemoria-brand">
                                <span class="logo-sm">
                                    <img src="{{ asset('backend/assets/images/logo/liliwmemoria-logo.png') }}" alt="LiliwMemoria logo" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('backend/assets/images/logo/liliwmemoria-logo.png') }}" alt="LiliwMemoria logo" height="24">
                                    <span class="liliwmemoria-brand__text text-primary"></span>
                                </span>
                            </a>
                            <a href="{{ url('/') }}" class="logo logo-dark liliwmemoria-brand">
                                <span class="logo-sm">
                                    <img src="{{ asset('backend/assets/images/logo/liliwmemoria-logo.png') }}" alt="LiliwMemoria logo" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('backend/assets/images/logo/liliwmemoria-logo.png') }}" alt="LiliwMemoria logo" height="24">
                                    <span class="liliwmemoria-brand__text text-primary"></span>
                                </span>
                            </a>
                        </div>

                        <ul id="side-menu">

                            <li class="menu-title">Menu</li>

                            <li>
                                <a href="{{ route('dashboard') }}" class="tp-link">
                                    <i data-feather="home"></i>
                                    <span> Dashboard </span>
                                </a>
                            </li>

                            @if (auth()->check() && auth()->user()->role === 'master_admin')
                                <li>
                                    <a href="{{ route('master.dashboard') }}" class="tp-link">
                                        <i data-feather="shield"></i>
                                        <span> Master Admin </span>
                                    </a>
                                </li>
                            @endif
                
                            <li class="menu-title">Pages</li>

                            @if (auth()->check() && auth()->user()->role === 'master_admin')
                                <li>
                                    <a href="{{ route('master.auditLogs.index') }}" class="tp-link">
                                        <i data-feather="activity"></i>
                                        <span> Audit Logs </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('master.users.index') }}" class="tp-link">
                                        <i data-feather="user-check"></i>
                                        <span> Users </span>
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('admin.clients.index') }}" class="tp-link">
                                    <i data-feather="users"></i>
                                    <span> Clients </span>
                                </a>
                            </li>

                            <li>
                                <a href="#" class="tp-link">
                                    <i data-feather="calendar"></i>
                                    <span> Reservations </span>
                                </a>
                            </li>

                            <li>
                                <a href="#" class="tp-link">
                                    <i data-feather="clipboard"></i>
                                    <span> Visitor Logs </span>
                                </a>
                            </li>

                            <li>
                                <a href="#sidebarError" data-bs-toggle="collapse">
                                    <i data-feather="layers"></i>
                                    <span> Properties </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarError">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('admin.lots.index') }}" class="tp-link">Lots</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.lots.map') }}" class="tp-link">Map View</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li>
                                <a href="#" class="tp-link">
                                    <i data-feather="map-pin"></i>
                                    <span> Interments </span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.payments.index') }}" class="tp-link">
                                    <i data-feather="credit-card"></i>
                                    <span> Payments </span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.reports.payments') }}" class="tp-link">
                                    <i data-feather="bar-chart-2"></i>
                                    <span> Reports </span>
                                </a>
                            </li>

                            <li>
                                <a href="#" class="tp-link">
                                    <i data-feather="settings"></i>
                                    <span> Settings </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                    <!-- End Sidebar -->

                    <div class="clearfix"></div>

                </div>
            </div>
