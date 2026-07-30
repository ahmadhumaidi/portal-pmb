<header class="topbar">

    <div class="d-flex align-items-center gap-3">
        <button class="btn sidebar-toggle" type="button" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <div>
            <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            <small class="text-muted">
                @yield('page-description', 'Portal Penerimaan Mahasiswa Baru')
            </small>
        </div>
    </div>

    <div class="topbar-actions">

        <div class="search-box d-none d-md-flex">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Cari data...">
        </div>

        <button class="btn icon-button" type="button">
            <i class="bi bi-bell"></i>
            <span class="notification-dot"></span>
        </button>

        <div class="dropdown">
            <button
                class="btn user-menu"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <div class="user-avatar">A</div>

                <div class="user-info d-none d-sm-block">
                    <div class="fw-semibold">Administrator</div>
                    <small>Admin</small>
                </div>

                <i class="bi bi-chevron-down"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-person me-2"></i>
                        Profil
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-gear me-2"></i>
                        Pengaturan
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>

</header>
