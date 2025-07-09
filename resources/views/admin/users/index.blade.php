@extends('layouts.app')
@section('title', 'Manajemen User')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Manajemen User</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            Tambah User
          </button>
        </div>

        {{-- FILTER FORM --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Cari Nama / Email</label>
            <input type="text" name="search" class="form-control" placeholder="Masukkan nama atau email"
              value="{{ request('search') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Filter Role</label>
            <select name="role" class="form-select">
              <option value="">Semua Role</option>
              <option value="admin" {{ request('role')=='admin' ? 'selected' :'' }}>Admin</option>
              <option value="kepala" {{ request('role')=='kepala' ? 'selected' :'' }}>Kepala</option>
              <option value="pegawai" {{ request('role')=='pegawai' ? 'selected' :'' }}>Pegawai</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Filter Status</label>
            <select name="status" class="form-select">
              <option value="">Semua Status</option>
              <option value="1" {{ request('status')=='1' ? 'selected' :'' }}>Active</option>
              <option value="0" {{ request('status')=='0' ? 'selected' :'' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-2 text-end">
            <button type="submit" class="btn btn-secondary w-100 mt-2">Filter</button>
          </div>
        </form>
        {{-- END FILTER --}}

        <div class="table-responsive mt-4">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  <span
                    class="badge bg-{{ $user->role == 'admin' ? 'primary' : ($user->role == 'kepala' ? 'info' : 'secondary') }}">
                    {{ ucfirst($user->role) }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                    data-bs-target="#detailUserModal{{ $user->id }}">Detail</button>
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                    data-bs-target="#editUserModal{{ $user->id }}">Edit</button>
                  <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $user->id }})">Hapus</button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{ $users->links("pagination::bootstrap-4") }}
        </div>
      </div>
    </div>
  </div>
</div>

@foreach($users as $user)
<!-- Modal Detail User -->
<div class="modal fade" id="detailUserModal{{ $user->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless mb-0">
          <tr>
            <th>NIP/NIK</th>
            <td>{{ $user->nip_nik }}</td>
          </tr>
          <tr>
            <th>Nama</th>
            <td>{{ $user->name }}</td>
          </tr>
          <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
          </tr>
          <tr>
            <th>Jabatan</th>
            <td>{{ $user->position }}</td>
          </tr>
          <tr>
            <th>Divisi</th>
            <td>{{ $user->division }}</td>
          </tr>
          <tr>
            <th>Alamat</th>
            <td>{{ $user->address }}</td>
          </tr>
          <tr>
            <th>Role</th>
            <td>{{ ucfirst($user->role) }}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>
              <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                {{ $user->is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.users.update', $user->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>NIP/NIK</label>
          <input type="text" name="nip_nik" class="form-control" value="{{ $user->nip_nik }}" required>
        </div>
        <div class="mb-3">
          <label>Nama</label>
          <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="mb-3">
          <label>Jabatan</label>
          <input type="text" name="position" class="form-control" value="{{ $user->position }}">
        </div>
        <div class="mb-3">
          <label>Divisi</label>
          <input type="text" name="division" class="form-control" value="{{ $user->division }}">
        </div>
        <div class="mb-3">
          <label>Alamat</label>
          <input type="text" name="address" class="form-control" value="{{ $user->address }}">
        </div>
        <div class="mb-3">
          <label>Role</label>
          <select name="role" class="form-control" required>
            <option value="admin" {{ $user->role=='admin' ? 'selected':'' }}>Admin</option>
            <option value="kepala" {{ $user->role=='kepala' ? 'selected':'' }}>Kepala</option>
            <option value="pegawai" {{ $user->role=='pegawai' ? 'selected':'' }}>Pegawai</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Status</label>
          <select name="is_active" class="form-control" required>
            <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
            <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Password (isi jika ingin mengubah)</label>
          <input type="password" name="password" class="form-control" autocomplete="new-password">
          <input type="password" name="password_confirmation" class="form-control mt-1"
            placeholder="Konfirmasi Password" autocomplete="new-password">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Update</button>
      </div>
    </form>
  </div>
</div>
@endforeach

<!-- Modal Tambah User -->
<div class="modal fade" id="createUserModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>NIP/NIK</label>
          <input type="text" name="nip_nik" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Nama</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Jabatan</label>
          <input type="text" name="position" class="form-control">
        </div>
        <div class="mb-3">
          <label>Divisi</label>
          <input type="text" name="division" class="form-control">
        </div>
        <div class="mb-3">
          <label>Alamat</label>
          <input type="text" name="address" class="form-control">
        </div>
        <div class="mb-3">
          <label>Role</label>
          <select name="role" class="form-control" required>
            <option value="admin">Admin</option>
            <option value="kepala">Kepala</option>
            <option value="pegawai">Pegawai</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Status</label>
          <select name="is_active" class="form-control" required>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required autocomplete="new-password">
          <input type="password" name="password_confirmation" class="form-control mt-1"
            placeholder="Konfirmasi Password" autocomplete="new-password">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function confirmDelete(userId) {
    Swal.fire({
      title: 'Yakin ingin menghapus user ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endsection