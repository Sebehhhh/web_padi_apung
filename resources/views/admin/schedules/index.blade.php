@extends('layouts.app')
@section('title','Manajemen Jadwal Kerja')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">

        <div class="d-md-flex align-items-center justify-content-between mb-3">
          <h4 class="card-title">Manajemen Jadwal Kerja</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
            Tambah Jadwal
          </button>
        </div>
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- FILTER --}}
        <form method="GET" action="{{ route('admin.schedules.index') }}" class="row g-2 align-items-end mb-3">
          <div class="col-md-3">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control"
                   value="{{ request('start_date') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" name="end_date" class="form-control"
                   value="{{ request('end_date') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Pegawai</label>
            <select name="user_id" class="form-select">
              <option value="">Semua Pegawai</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}"
                  {{ request('user_id')==$user->id?'selected':'' }}>
                  {{ $user->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 text-end">
            <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
          </div>
        </form>
        {{-- END FILTER --}}

        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pegawai</th>
                <th>Divisi</th>
                <th>Tugas</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($schedules as $sched)
                <tr>
                  <td>{{ $loop->iteration + ($schedules->currentPage()-1)*$schedules->perPage() }}</td>
                  <td>{{ \Carbon\Carbon::parse($sched->schedule_date)->format('d M Y') }}</td>
                  <td>{{ $sched->user ? $sched->user->name : '-' }}</td>
                  <td>{{ $sched->user ? $sched->user->division : '-' }}</td>
                  <td>{{ Str::limit($sched->task_name,30) }}</td>
                  <td>
                    @if($sched->start_time)
                      {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }}
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    @if($sched->end_time)
                      {{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    <span class="badge bg-{{ $sched->status=='Selesai'?'success':'warning' }}">
                      {{ $sched->status }}
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                            data-bs-target="#detailScheduleModal{{ $sched->id }}">
                      Detail
                    </button>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#editScheduleModal{{ $sched->id }}">
                      Edit
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $sched->id }})">
                      Hapus
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center text-muted">Belum ada jadwal.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
          {{ $schedules->links("pagination::bootstrap-4") }}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Create --}}
<div class="modal fade" id="createScheduleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('admin.schedules.store') }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Jadwal Kerja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Pegawai</label>
            <select name="user_id" class="form-select" required>
              <option value="">Pilih...</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->division }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tanggal</label>
            <input type="date" name="schedule_date" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Mulai</label>
            <input type="time" name="start_time" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Selesai</label>
            <input type="time" name="end_time" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label">Tugas</label>
            <textarea name="task_name" class="form-control" rows="2" required></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="Pending">Pending</option>
              <option value="Selesai">Selesai</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Edit --}}
@foreach($schedules as $sched)
<div class="modal fade" id="editScheduleModal{{ $sched->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST"
          action="{{ route('admin.schedules.update',$sched->id) }}"
          class="modal-content">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Jadwal #{{ $sched->id }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Pegawai</label>
            <select name="user_id" class="form-select" required>
              @foreach($users as $user)
                <option value="{{ $user->id }}"
                  {{ $sched->user_id==$user->id?'selected':'' }}>
                  {{ $user->name }} ({{ $user->division }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tanggal</label>
            <input type="date" name="schedule_date"
                   class="form-control"
                   value="{{ $sched->schedule_date }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Mulai</label>
            <input type="time" name="start_time"
                   class="form-control"
                   value="{{ $sched->start_time }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Selesai</label>
            <input type="time" name="end_time"
                   class="form-control"
                   value="{{ $sched->end_time }}">
          </div>
          <div class="col-12">
            <label class="form-label">Tugas</label>
            <textarea name="task_name" class="form-control" rows="2" required>{{ $sched->task_name }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="Pending"   {{ $sched->status=='Pending'?'selected':'' }}>Pending</option>
              <option value="Selesai"   {{ $sched->status=='Selesai'?'selected':'' }}>Selesai</option>
            </select>
          </div>
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

{{-- Modal Detail --}}
@foreach($schedules as $sched)
<div class="modal fade" id="detailScheduleModal{{ $sched->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Jadwal #{{ $sched->id }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless mb-0">
          <tr>
            <th style="width:180px">Tanggal</th>
            <td>{{ \Carbon\Carbon::parse($sched->schedule_date)->format('d M Y') }}</td>
          </tr>
          <tr>
            <th>Pegawai</th>
            <td>
              {{ $sched->user ? $sched->user->name : '-' }} 
              @if($sched->user && $sched->user->division)
                ({{ $sched->user->division }})
              @endif
            </td>
          </tr>
          <tr>
            <th>Tugas</th>
            <td>{{ $sched->task_name }}</td>
          </tr>
          <tr>
            <th>Mulai</th>
            <td>
              @if($sched->start_time)
                {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }}
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <th>Selesai</th>
            <td>
              @if($sched->end_time)
                {{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <th>Status</th>
            <td>
              <span class="badge bg-{{ $sched->status=='Selesai'?'success':'warning' }}">
                {{ $sched->status }}
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
@endforeach

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function confirmDelete(id) {
    Swal.fire({
      title: 'Hapus jadwal?',
      text: 'Data jadwal akan dihapus permanen.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((res)=>{
      if(res.isConfirmed){
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = `/admin/schedules/${id}`;
        f.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(f);
        f.submit();
      }
    });
  }
</script>
@endsection