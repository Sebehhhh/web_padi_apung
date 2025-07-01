@extends('layouts.app')
@section('title', 'Kegiatan Pegawai')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Kegiatan Pegawai</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createActivityModal">
            Tambah Kegiatan
          </button>
        </div>
        <div class="table-responsive mt-4">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($activities as $activity)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($activity->activity_date)->format('d M Y') }}</td>
                <td>{{ $activity->category->name ?? '-' }}</td>
                <td>{{ Str::limit($activity->description, 50) }}</td>
                <td>{{ $activity->location ?? '-' }}</td>
                <td>
                  @php
                  $statusColor = [
                  'Draft' => 'secondary',
                  'Pending' => 'warning',
                  'Selesai' => 'success',
                  'Dibatalkan' => 'danger'
                  ][$activity->status] ?? 'secondary';
                  @endphp
                  <span class="badge bg-{{ $statusColor }}">
                    {{ $activity->status }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                    data-bs-target="#detailActivityModal{{ $activity->id }}">Detail</button>
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                    data-bs-target="#editActivityModal{{ $activity->id }}">Edit</button>
                  <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $activity->id }})">Hapus</button>
                  @if($activity->photos->count() < 5) <button class="btn btn-success btn-sm mt-1" data-bs-toggle="modal"
                    data-bs-target="#uploadPhotoModal{{ $activity->id }}">
                    Dokumentasi
                    </button>
                    @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center text-muted">Belum ada data kegiatan.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
          {{ $activities->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="createActivityModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.activities.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Kegiatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Tanggal Kegiatan</label>
          <input type="date" name="activity_date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Kategori Aktivitas</label>
          <select name="category_id" class="form-control" required>
            <option value="">Pilih Kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Deskripsi</label>
          <textarea name="description" class="form-control" required maxlength="500"></textarea>
        </div>
        <div class="mb-3">
          <label>Lokasi</label>
          <input type="text" name="location" class="form-control" maxlength="100">
        </div>
        <div class="mb-3">
          <label>Waktu Mulai</label>
          <input type="time" name="start_time" class="form-control">
        </div>
        <div class="mb-3">
          <label>Waktu Selesai</label>
          <input type="time" name="end_time" class="form-control">
        </div>
        <div class="mb-3">
          <label>Status</label>
          <select name="status" class="form-control" required>
            <option value="Draft">Draft</option>
            <option value="Pending">Pending</option>
            <option value="Selesai">Selesai</option>
            <option value="Dibatalkan">Dibatalkan</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
@foreach($activities as $activity)
<div class="modal fade" id="editActivityModal{{ $activity->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.activities.update', $activity->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Kegiatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Tanggal Kegiatan</label>
          <input type="date" name="activity_date" class="form-control" value="{{ $activity->activity_date }}" required>
        </div>
        <div class="mb-3">
          <label>Kategori Aktivitas</label>
          <select name="category_id" class="form-control" required>
            <option value="">Pilih Kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ $activity->category_id == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Deskripsi</label>
          <textarea name="description" class="form-control" required
            maxlength="500">{{ $activity->description }}</textarea>
        </div>
        <div class="mb-3">
          <label>Lokasi</label>
          <input type="text" name="location" class="form-control" value="{{ $activity->location }}" maxlength="100">
        </div>
        <div class="mb-3">
          <label>Waktu Mulai</label>
          <input type="time" name="start_time" class="form-control" value="{{ $activity->start_time }}">
        </div>
        <div class="mb-3">
          <label>Waktu Selesai</label>
          <input type="time" name="end_time" class="form-control" value="{{ $activity->end_time }}">
        </div>
        <div class="mb-3">
          <label>Status</label>
          <select name="status" class="form-control" required>
            <option value="Draft" {{ $activity->status == 'Draft' ? 'selected' : '' }}>Draft</option>
            <option value="Pending" {{ $activity->status == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Selesai" {{ $activity->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="Dibatalkan" {{ $activity->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
          </select>
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

<!-- Modal Detail (optional, next time bisa isi lebih lengkap) -->
@foreach($activities as $activity)
<div class="modal fade" id="detailActivityModal{{ $activity->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Kegiatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless mb-0">
          <tr>
            <th>Tanggal</th>
            <td>{{ \Carbon\Carbon::parse($activity->activity_date)->format('d M Y') }}</td>
          </tr>
          <tr>
            <th>Kategori</th>
            <td>{{ $activity->category->name ?? '-' }}</td>
          </tr>
          <tr>
            <th>Deskripsi</th>
            <td>{{ $activity->description }}</td>
          </tr>
          <tr>
            <th>Lokasi</th>
            <td>{{ $activity->location ?? '-' }}</td>
          </tr>
          <tr>
            <th>Waktu</th>
            <td>{{ $activity->start_time }} s/d {{ $activity->end_time }}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>
              <span class="badge bg-{{ $statusColor }}">
                {{ $activity->status }}
              </span>
            </td>
          </tr>
        </table>
        @if($activity->photos->count() > 0)
        <hr>
        <div class="mb-2 fw-bold">Dokumentasi Foto:</div>
        <div class="row">
          @foreach($activity->photos as $photo)
          <div class="col-6 col-md-4 mb-3">
            <div class="card">
              <img src="{{ asset('storage/'.$photo->photo_url) }}" class="card-img-top"
                style="height:110px;object-fit:cover;border-radius:.4rem .4rem 0 0;">
              <div class="card-body p-2">
                <div class="small mb-1 text-muted">{{ $photo->caption ?: '-' }}</div>
                <div class="text-secondary small">{{ $photo->taken_at ?
                  \Carbon\Carbon::parse($photo->taken_at)->format('d M Y H:i') : '' }}</div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endforeach

<!-- Modal Upload Dokumentasi (per Activity) -->
@foreach($activities as $activity)
<div class="modal fade" id="uploadPhotoModal{{ $activity->id }}" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.activity-photos.store') }}"
      enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="activity_id" value="{{ $activity->id }}">
      <div class="modal-header">
        <h5 class="modal-title">Upload Foto Dokumentasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Foto <span class="text-danger">*</span></label>
          <input type="file" name="photo" class="form-control" accept="image/*" required>
        </div>
        <div class="mb-3">
          <label>Keterangan</label>
          <input type="text" name="caption" class="form-control" maxlength="100">
        </div>
        <div class="mb-3">
          <label>Tanggal Pengambilan</label>
          <input type="datetime-local" name="taken_at" class="form-control">
        </div>
        <div class="text-muted small">
          Maksimal 5 foto per kegiatan. Ukuran maksimal 2MB/foto.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Upload</button>
      </div>
    </form>
  </div>
</div>
@endforeach

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function confirmDelete(activityId) {
    Swal.fire({
      title: 'Yakin ingin menghapus kegiatan ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/activities/${activityId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endsection