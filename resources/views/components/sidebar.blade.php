<aside class="sidebar">
    <div class="logo-section" style="display: flex; justify-content: space-between; align-items: center; width: 100%; padding-right: 15px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="logo-circle"></div>
            <span class="logo-name">MiniBank</span>
        </div>

        <button id="closeSidebar" class="close-sidebar-btn" style="background: transparent; border: none; font-size: 20px; color: #a0aec0; cursor: pointer; display: none;">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <nav class="menu-list">

        <a href="{{ route('dashboard.index') }}" class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>Dashboard
        </a>

        @if(Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin')

        <a href="{{ route('murid.index') }}" class="menu-item {{ Request::is('master-murid*') || Request::is('detail-murid*') ? 'active' : '' }}">
            <i class="fa-solid fa-user"></i>Data Master
        </a>

        <a href="{{ route('admin.transaksi.index') }}" class="menu-item {{ Request::is('transaksi-petugas*') ? 'active' : '' }}">
            <i class="fa-solid fa-money-bill-transfer"></i>Transaksi Petugas
        </a>

        <!-- update tutup by dean 01062026 -->
        <!-- <a href="#" class="menu-item">
            <i class="fa-solid fa-chart-line"></i>Report
        </a> -->

        <!-- update by dean 01062026 -->
        <a href="{{ route('admin.report') }}" class="menu-item {{ Request::is('laporan-nasabah*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>Report
        </a>
        @endif

        @if(Auth::user()->role == 'nasabah')

        <a href="{{ route('nasabah.pengajuan.create') }}" class="menu-item {{ Request::is('transaksi/pengajuan*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar"></i>Pengajuan Transaksi
        </a>

        <a href="{{ route('nasabah.transaksi') }}" class="menu-item {{ Request::is('transaksi/riwayat*') ? 'active' : '' }}">
            <i class="fa-solid fa-money-bill-transfer"></i>Riwayat Transaksi
        </a>

        @endif


        <div class="menu-label">GENERAL</div>

        @if(Auth::user()->role == 'superadmin')
        <a href="{{ route('user.index') }}" class="menu-item {{ Request::is('manajemen-user*') || Request::is('tambah-user*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i>Manajemen User
        </a>
        @endif

        @if(Auth::user()->role == 'nasabah')
        <a href="{{ route('nasabah.setting') }}" class="menu-item {{ Request::is('nasabah/pengaturan*') ? 'active' : '' }}">
            <i class="fa-solid fa-gear"></i>Settings
        </a>
        @endif

        <a href="javascript:void(0);" class="menu-item" onclick="event.preventDefault(); document.getElementById('logout-form-action').submit();">
            <i class="fa-solid fa-right-from-bracket"></i>Logout
        </a>
    </nav>
</aside>