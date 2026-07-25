<aside class="sidebar d-flex flex-column">

    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>

        <div>
            <h5 class="mb-0">Portal PMB</h5>
            <small>Management System</small>
        </div>
    </div>

    <nav class="sidebar-menu flex-grow-1">

        <a
            href="{{ route('dashboard') }}"
            class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
        >
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </a>

        <div class="sidebar-section">Master</div>

        <a
            href="{{ route('kampus.index') }}"
            class="sidebar-link {{ request()->routeIs('kampus.*') ? 'active' : '' }}"
        >
            <i class="bi bi-buildings"></i>
            <span>Kampus</span>
        </a>

        <a
            href="{{ route('jurusan.index') }}"
            class="sidebar-link {{ request()->routeIs('jurusan.*') ? 'active' : '' }}"
        >
            <i class="bi bi-mortarboard"></i>
            <span>Jurusan</span>
        </a>

        <a
            href="{{ route('staff.index') }}"
            class="sidebar-link {{ request()->routeIs('staff.*') ? 'active' : '' }}"
        >
            <i class="bi bi-people"></i>
            <span>Staff</span>
        </a>

        <div class="sidebar-section">PMB</div>

        <a
            href="{{ route('mahasiswa.index') }}"
            class="sidebar-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}"
        >
            <i class="bi bi-person-vcard"></i>
            <span>Mahasiswa</span>
        </a>

        <a
            href="{{ route('berkas.index') }}"
            class="sidebar-link {{ request()->routeIs('berkas.*') ? 'active' : '' }}"
        >
            <i class="bi bi-folder2-open"></i>
            <span>Berkas</span>
        </a>

        <a
            href="{{ route('pembayaran.index') }}"
            class="sidebar-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}"
        >
            <i class="bi bi-wallet2"></i>
            <span>Pembayaran</span>
        </a>

        <a
            href="{{ route('hasil.index') }}"
            class="sidebar-link {{ request()->routeIs('hasil.*') ? 'active' : '' }}"
        >
            <i class="bi bi-patch-check"></i>
            <span>Hasil</span>
        </a>

        <div class="sidebar-section">Sistem</div>

        <a
            href="{{ route('laporan.index') }}"
            class="sidebar-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}"
        >
            <i class="bi bi-bar-chart"></i>
            <span>Laporan</span>
        </a>

        <a
            href="{{ route('pengaturan.index') }}"
            class="sidebar-link {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}"
        >
            <i class="bi bi-gear"></i>
            <span>Pengaturan</span>
        </a>

    </nav>

    <div class="sidebar-footer">
        <small>Portal PMB v1.0</small>
    </div>

</aside>

