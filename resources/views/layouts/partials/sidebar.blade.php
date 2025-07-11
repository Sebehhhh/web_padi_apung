{{-- resources/views/layouts/sidebar.blade.php --}}
<aside class="left-sidebar">
  <div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-6"></i>
      </div>
    </div>
    <nav class="sidebar-nav scroll-sidebar" data-simplebar>
      <ul id="sidebarnav">
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Menu Utama</span>
        </li>

        @if(auth()->user()->role === 'admin')
        <style>
          .sidebar-link:hover,
          .sidebar-item.active>.sidebar-link {
            background-color: #28a745 !important;
            color: #fff !important;
          }
        </style>

        <!-- Dashboard -->
        <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.dashboard') }}">
            <i class="ti ti-atom"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>

        <!-- Master Data -->
        <li
          class="sidebar-item has-arrow {{ request()->routeIs('admin.users.*','admin.activity-categories.*','admin.crop-types.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="javascript:void(0)">
            <i class="ti ti-database"></i>
            <span class="hide-menu">Master Data</span>
          </a>
          <ul
            class="collapse first-level {{ request()->routeIs('admin.users.*','admin.activity-categories.*','admin.crop-types.*') ? 'show' : '' }}">
            <li class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
              <a class="sidebar-link" href="{{ route('admin.users.index') }}">
                <i class="ti ti-users"></i>
                <span class="hide-menu">Manajemen User</span>
              </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.activity-categories.*') ? 'active' : '' }}">
              <a class="sidebar-link" href="{{ route('admin.activity-categories.index') }}">
                <i class="ti ti-category"></i>
                <span class="hide-menu">Kategori Kegiatan</span>
              </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.crop-types.*') ? 'active' : '' }}">
              <a class="sidebar-link" href="{{ route('admin.crop-types.index') }}">
                <i class="ti ti-leaf"></i>
                <span class="hide-menu">Jenis Tanaman</span>
              </a>
            </li>
          </ul>
        </li>

        <!-- Kegiatan -->
        <li class="sidebar-item {{ request()->routeIs('admin.activities.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.activities.index') }}">
            <i class="ti ti-calendar-event"></i>
            <span class="hide-menu">Kegiatan</span>
          </a>
        </li>

        <!-- Permintaan -->
        <li class="sidebar-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.requests.index') }}">
            <i class="ti ti-shopping-cart"></i>
            <span class="hide-menu">Permintaan Barang</span>
          </a>
        </li>

        <!-- Panen -->
        <li class="sidebar-item {{ request()->routeIs('admin.harvests.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.harvests.index') }}">
            <i class="ti ti-basket"></i>
            <span class="hide-menu">Pencatatan Panen</span>
          </a>
        </li>

        <!-- Riwayat Aktivitas -->
        <li class="sidebar-item {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.logs.index') }}">
            <i class="ti ti-list-details"></i>
            <span class="hide-menu">Riwayat Aktivitas</span>
          </a>
        </li>
        <!-- Jadwal Kerja Harian -->
        <li class="sidebar-item {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.schedules.index') }}">
            <i class="ti ti-clock"></i>
            <span class="hide-menu">Jadwal Kerja</span>
          </a>
        </li>

        @elseif(auth()->user()->role === 'kepala')
        <style>
          .sidebar-link:hover,
          .sidebar-item.active>.sidebar-link {
            background-color: #28a745 !important;
            color: #fff !important;
          }
        </style>

        <!-- Dashboard Kepala -->
        <li class="sidebar-item {{ request()->routeIs('kepala.dashboard') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('kepala.dashboard') }}">
            <i class="ti ti-atom"></i>
            <span class="hide-menu">Dashboard Kepala</span>
          </a>
        </li>

        <!-- Kegiatan (read-only) -->
        <li class="sidebar-item {{ request()->routeIs('kepala.activities.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('kepala.activities.index') }}">
            <i class="ti ti-calendar-event"></i>
            <span class="hide-menu">Kegiatan</span>
          </a>
        </li>

        <!-- Data Pegawai (profil tim) -->
        <li class="sidebar-item {{ request()->routeIs('kepala.users.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('kepala.users.index') }}">
            <i class="ti ti-users"></i>
            <span class="hide-menu">Data Pegawai</span>
          </a>
        </li>

        <!-- Permintaan (ringkasan & histori) -->
        <li class="sidebar-item {{ request()->routeIs('kepala.requests.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('kepala.requests.index') }}">
            <i class="ti ti-shopping-cart"></i>
            <span class="hide-menu">Permintaan</span>
          </a>
        </li>

        <!-- Hasil Panen (tim) -->
        <li class="sidebar-item {{ request()->routeIs('kepala.harvests.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('kepala.harvests.index') }}">
            <i class="ti ti-basket"></i>
            <span class="hide-menu">Hasil Panen</span>
          </a>
        </li>

        <!-- Jadwal Kerja Harian (read-only) -->
        <li class="sidebar-item {{ request()->routeIs('kepala.schedules.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('kepala.schedules.index') }}">
            <i class="ti ti-calendar-time"></i>
            <span class="hide-menu">Jadwal Kerja</span>
          </a>
        </li>

        @elseif(auth()->user()->role === 'pegawai')
        <style>
          .sidebar-link:hover,
          .sidebar-item.active>.sidebar-link {
            background-color: #28a745 !important;
            color: #fff !important;
          }
        </style>

        <!-- Dashboard Pegawai -->
        <li class="sidebar-item {{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('pegawai.dashboard') }}">
            <i class="ti ti-atom"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>

        <!-- Data Diri -->
        <li class="sidebar-item {{ request()->routeIs('pegawai.users.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('pegawai.users.show', auth()->user()->id) }}">
            <i class="ti ti-user"></i>
            <span class="hide-menu">Data Diri</span>
          </a>
        </li>

        <!-- Permintaan Barang -->
        <li class="sidebar-item {{ request()->routeIs('pegawai.requests.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('pegawai.requests.index') }}">
            <i class="ti ti-shopping-cart"></i>
            <span class="hide-menu">Permintaan Barang</span>
          </a>
        </li>

        <!-- Pencatatan Panen -->
        <li class="sidebar-item {{ request()->routeIs('pegawai.harvests.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('pegawai.harvests.index') }}">
            <i class="ti ti-basket"></i>
            <span class="hide-menu">Pencatatan Panen</span>
          </a>
        </li>

        <!-- Jadwal Kerja -->
        <li class="sidebar-item {{ request()->routeIs('pegawai.schedules.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('pegawai.schedules.index') }}">
            <i class="ti ti-clock"></i>
            <span class="hide-menu">Jadwal Kerja</span>
          </a>
        </li>

        @endif

      </ul>
    </nav>
  </div>
</aside>