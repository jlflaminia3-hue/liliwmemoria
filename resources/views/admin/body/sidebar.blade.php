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
                                        <i data-feather="search"></i>
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
                                <a href="{{ route('admin.analytics.visitors') }}" class="tp-link">
                                    <i data-feather="clipboard"></i>
                                    <span> Visitor Logs </span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.reservations.index') }}" class="tp-link">
                                    <i data-feather="calendar"></i>
                                    <span> Lot Purchases </span>
                                </a>
                            </li>

                            <li>
                                <a href="#sidebarProperties" data-bs-toggle="collapse">
                                    <i data-feather="box"></i>
                                    <span> Lot Management </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarProperties">
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
                                <a href="#sidebarBurial" data-bs-toggle="collapse">
                                    <i data-feather="layers"></i>
                                    <span> Burial Records </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarBurial">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('admin.interments.index') }}" class="tp-link">Deceased</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.exhumations.index') }}" class="tp-link">Exhumations</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li>
                                <a href="#sidebarPayments" data-bs-toggle="collapse">
                                    <i data-feather="credit-card"></i>
                                    <span> Payments </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarPayments">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('admin.payments.index') }}" class="tp-link">Payments</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.interment-payments.index') }}" class="tp-link">Interment Payments</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li>
                                <a href="#sidebarReports" data-bs-toggle="collapse">
                                    <i data-feather="file-text"></i>
                                    <span> Reports </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarReports">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('admin.reports.index') }}" class="tp-link">Overview</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.reports.clients') }}" class="tp-link">Clients Report</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.reports.plots') }}" class="tp-link">Plots Report</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.reports.payments') }}" class="tp-link">Payments Report</a>
                                        </li>
                                    </ul>
                                </div>
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