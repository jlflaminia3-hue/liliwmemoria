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
                                <a href="{{ route('admin.reservations.index') }}" class="tp-link">
                                    <i data-feather="calendar"></i>
                                    <span> Reservations </span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.analytics.visitors') }}" class="tp-link">
                                    <i data-feather="clipboard"></i>
                                    <span> Visitor Logs </span>
                                </a>
                            </li>

                            <li>
                                <a href="#sidebarError" data-bs-toggle="collapse">
                                    <i data-feather="map-pin"></i>
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
                                <a href="{{ route('admin.interments.index') }}" class="tp-link">
                                    <i data-feather="layers"></i>
                                    <span> Deceased </span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.exhumations.index') }}" class="tp-link">
                                    <i data-feather="repeat"></i>
                                    <span> Exhumations </span>
                                </a>
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
                                            <a href="{{ route('admin.lot-payments.index') }}" class="tp-link">Lot Payments</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.payments.index') }}" class="tp-link">Installment Payments</a>
                                        </li>

                                        <li>
                                            <a href="{{ route('admin.interment-payments.index') }}" class="tp-link">Interment Payments</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>


                            <li>
                                <a href="#sidebarAnalytics" data-bs-toggle="collapse">
                                    <i data-feather="bar-chart-2"></i>
                                    <span> Analytics </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarAnalytics">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('admin.analytics.index') }}" class="tp-link">Overview</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.analytics.clients') }}" class="tp-link">Clients Analytics</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.analytics.plots') }}" class="tp-link">Plots Analytics</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.analytics.payments') }}" class="tp-link">Payments Analytics</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.analytics.documents') }}" class="tp-link">Documents Analytics</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.analytics.interments') }}" class="tp-link">Interments Analytics</a>
                                        </li>
                                        {{-- <li>
                                            <a href="{{ route('admin.analytics.visitors') }}" class="tp-link">Visitors Analytics</a>
                                        </li> --}}
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
