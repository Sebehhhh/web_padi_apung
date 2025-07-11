@extends('layouts.app')
@section('title','Jadwal Kerja Saya')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">

        <div class="d-md-flex align-items-center justify-content-between mb-3">
          <h4 class="card-title">Jadwal Kerja Saya</h4>
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
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- FILTER --}}
        <form method="GET" action="{{ route('pegawai.schedules.index') }}" class="row g-2 align-items-end mb-3">
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
                    @if($sched->status == 'Pending')
                    <form method="POST" action="{{ route('pegawai.schedules.complete', $sched->id) }}" class="d-inline schedule-complete-form" id="completeForm{{ $sched->id }}">
                      @csrf
                      @method('PATCH')
                      <button type="button" class="btn btn-success btn-sm btn-complete-schedule" data-id="{{ $sched->id }}">
                        Tandai Selesai
                      </button>
                    </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted">Belum ada jadwal.</td>
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
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-complete-schedule').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.getAttribute('data-id');
        Swal.fire({
          title: 'Tandai jadwal ini sebagai selesai?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Ya, Selesai',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            document.getElementById('completeForm' + id).submit();
          }
        });
      });
    });
  });
</script>
@endsection
