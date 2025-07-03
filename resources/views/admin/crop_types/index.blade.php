@extends('layouts.app')
@section('title', 'Jenis Tanaman')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Jenis Tanaman</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCropModal">
            Tambah Jenis
          </button>
        </div>
        <div class="table-responsive mt-4">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Jenis</th>
                <th>Deskripsi</th>
                {{-- <th>Status</th> --}}
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($crops as $crop)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $crop->name }}</td>
                <td>{{ $crop->description ?? '-' }}</td>
                {{-- <td>
                  <span class="badge bg-{{ $crop->is_active ? 'success' : 'danger' }}">
                    {{ $crop->is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td> --}}
                <td>
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                    data-bs-target="#editCropModal{{ $crop->id }}">Edit</button>
                  <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $crop->id }})">Hapus</button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center text-muted">Belum ada data jenis tanaman.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
          {{ $crops->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="createCropModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.crop-types.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Jenis Tanaman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Jenis</label>
          <input type="text" name="name" class="form-control" required maxlength="50">
        </div>
        <div class="mb-3">
          <label>Deskripsi</label>
          <input type="text" name="description" class="form-control" maxlength="100">
        </div>
        {{-- <div class="mb-3">
          <label>Status</label>
          <select name="is_active" class="form-control" required>
            <option value="1" selected>Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div> --}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
@foreach($crops as $crop)
<div class="modal fade" id="editCropModal{{ $crop->id }}" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.crop-types.update', $crop->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Jenis Tanaman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Jenis</label>
          <input type="text" name="name" class="form-control" value="{{ $crop->name }}" required maxlength="50">
        </div>
        <div class="mb-3">
          <label>Deskripsi</label>
          <input type="text" name="description" class="form-control" value="{{ $crop->description }}" maxlength="100">
        </div>
        {{-- <div class="mb-3">
          <label>Status</label>
          <select name="is_active" class="form-control" required>
            <option value="1" {{ $crop->is_active ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ !$crop->is_active ? 'selected' : '' }}>Nonaktif</option>
          </select>
        </div> --}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Update</button>
      </div>
    </form>
  </div>
</div>
@endforeach

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function confirmDelete(cropId) {
    Swal.fire({
      title: 'Yakin ingin menghapus jenis ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/crop-types/${cropId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endsection