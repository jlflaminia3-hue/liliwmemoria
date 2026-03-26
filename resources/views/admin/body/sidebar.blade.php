            <div class="app-sidebar-menu">
                <div class="h-100" data-simplebar>

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">

                        <div class="logo-box">
                            <a href="{{ url('/') }}" class="logo logo-light liliwmemoria-brand">
                                <span class="logo-sm">
                                    <img src="{{ asset('frontend/assets/images/logo/liliwmemoria-logo.png') }}" alt="LiliwMemoria logo" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('frontend/assets/images/logo/liliwmemoria-logo.png') }}" alt="LiliwMemoria logo" height="24">
                                    <span class="liliwmemoria-brand__text text-primary">LiliwMemoria</span>
                                </span>
                            </a>
                            <a href="{{ url('/') }}" class="logo logo-dark liliwmemoria-brand">
                                <span class="logo-sm">
                                    <img src="{{ asset('frontend/assets/images/logo/liliwmemoria-logo.png') }}" alt="LiliwMemoria logo" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('frontend/assets/images/logo/liliwmemoria-logo.png') }}" alt="LiliwMemoria logo" height="24">
                                    <span class="liliwmemoria-brand__text text-primary">LiliwMemoria</span>
                                </span>
                            </a>
                        </div>

                        <ul id="side-menu">

                            <li class="menu-title">Menu</li>

                            <li>
                                <a href="#sidebarDashboards" data-bs-toggle="collapse">
                                    <i data-feather="home"></i>
                                    <span> Dashboard </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarDashboards">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="index.html" class="tp-link">Analytical</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                
                            <li class="menu-title">Pages</li>

                            <li>
                                <a href="#sidebarAuth" data-bs-toggle="collapse">
                                    <i data-feather="users"></i>
                                    <span> Clients </span>
                                    </a>
                            </li>

                            {{-- <li>
                                <a href="#sidebarAuth" data-bs-toggle="collapse">
                                    <i data-feather="file-text"></i>
                                    <span> Visitor Logs </span>
                                    <span class="menu-arrow"></span>
                                </a>
                            </li> --}}

                            <li>
                                <a href="calendar.html" class="tp-link">
                                    <i data-feather="calendar"></i>
                                    <span> Reservations </span>
                                </a>
                            </li>

                            <li>
                                <a href="calendar.html" class="tp-link">
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
                                <a href="#sidebarExpages" data-bs-toggle="collapse">
                                    <i data-feather="map-pin"></i>
                                    <span> Interments </span>
                                </a>
                            </li>

                            <li>
                                <a href="calendar.html" class="tp-link">
                                    <i data-feather="credit-card"></i>
                                    <span> Payments </span>
                                </a>
                            </li>


                            <li>
                                <a href="calendar.html" class="tp-link">
                                    <i data-feather="bar-chart-2"></i>
                                    <span> Reports </span>
                                </a>
                            </li>

                            <li>
                                <a href="calendar.html" class="tp-link">
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
