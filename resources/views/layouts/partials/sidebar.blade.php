<aside class="left-sidebar">
  <div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
      <a href="#" class="text-nowrap logo-img">
        <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Logo" />
      </a>
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-6"></i>
      </div>
    </div>
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
      <ul id="sidebarnav">
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Menu Utama</span>
        </li>
        <!-- Hanya admin -->
        @if(auth()->user()->role === 'admin')
        <!-- Dashboard -->
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
            <i class="ti ti-atom"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.users.index') }}" aria-expanded="false">
            <i class="ti ti-users"></i>
            <span class="hide-menu">Manajemen User</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.activity-categories.index') }}" aria-expanded="false">
            <i class="ti ti-category"></i>
            <span class="hide-menu">Kategori Aktivitas</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.crop-types.index') }}" aria-expanded="false">
            <i class="ti ti-leaf"></i>
            <span class="hide-menu">Jenis Tanaman</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.logs.index') }}" aria-expanded="false">
            <i class="ti ti-list-details"></i>
            <span class="hide-menu">Riwayat Aktivitas</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.activities.index') }}" aria-expanded="false">
            <i class="ti ti-calendar-event"></i>
            <span class="hide-menu">Kegiatan</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.requests.index') }}" aria-expanded="false">
            <i class="ti ti-shopping-cart"></i>
            <span class="hide-menu">Permintaan Barang/Bahan</span>
          </a>
        </li>
        @endif

      </ul>
    </nav>
  </div>
</aside>