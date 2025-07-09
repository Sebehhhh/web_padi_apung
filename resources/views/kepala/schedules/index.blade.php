{{-- resources/views/kepala/schedules/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Jadwal Kerja Harian')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">

                <div class="d-md-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title">Jadwal Kerja Harian</h4>
                </div>

                {{-- FILTER --}}
                <form method="GET" action="{{ route('kepala.schedules.index') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pegawai</label>
                        <select name="user_id" class="form-select">
                            <option value="">Semua Pegawai</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id')==$user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Divisi</label>
                        <select name="division" class="form-select">
                            <option value="">Semua Divisi</option>
                            @foreach($divisions as $div)
                            <option value="{{ $div }}" {{ request('division')===$div ? 'selected' : '' }}>
                                {{ $div }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
                {{-- END FILTER --}}

                {{-- EXPORT PDF --}}
                <form method="GET" action="{{ route('kepala.schedules.export') }}" target="_blank" class="mb-4">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                    <input type="hidden" name="division" value="{{ request('division') }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </form>
                {{-- END EXPORT PDF --}}

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
                            @forelse($schedules as $schedule)
                            <tr>
                                <td>{{ $loop->iteration + ($schedules->currentPage()-1)*$schedules->perPage() }}</td>
                                <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</td>
                                <td>{{ $schedule->user->name }}</td>
                                <td>{{ $schedule->user->division }}</td>
                                <td>{{ Str::limit($schedule->task, 40) }}</td>
                                <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $schedule->status === 'Selesai' ? 'success' : 'warning' }}">
                                        {{ $schedule->status }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#detailScheduleModal{{ $schedule->id }}">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Belum ada jadwal.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $schedules->withQueryString()->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Jadwal --}}
@foreach($schedules as $schedule)
<div class="modal fade" id="detailScheduleModal{{ $schedule->id }}" tabindex="-1"
    aria-labelledby="detailScheduleLabel{{ $schedule->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailScheduleLabel{{ $schedule->id }}">
                    Detail Jadwal: {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width: 180px;">Tanggal</th>
                        <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Pegawai</th>
                        <td>{{ $schedule->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Divisi</th>
                        <td>{{ $schedule->user->division }}</td>
                    </tr>
                    <tr>
                        <th>Tugas</th>
                        <td>{{ $schedule->task }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Mulai</th>
                        <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Selesai</th>
                        <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $schedule->status === 'Selesai' ? 'success' : 'warning' }}">
                                {{ $schedule->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection