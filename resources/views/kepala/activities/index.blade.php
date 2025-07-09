{{-- resources/views/kepala/activities/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Kegiatan Lapangan')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">

                <div class="d-md-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title">Kegiatan Lapangan</h4>
                </div>

                {{-- FILTER + EXPORT --}}
                <form method="GET" action="{{ route('kepala.activities.index') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-md-3">
                        <label class="form-label mb-1">Tanggal Mulai</label>
                        <input type="date" name="date_start" class="form-control" value="{{ request('date_start') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Tanggal Selesai</label>
                        <input type="date" name="date_end" class="form-control" value="{{ request('date_end') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Kategori Kegiatan</label>
                        <select name="category_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('kepala.activities.export', request()->only(['date_start','date_end','category_id','status'])) }}"
                           class="btn btn-success w-100">
                            Export
                        </a>
                    </div>
                </form>
                {{-- END FILTER + EXPORT --}}

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Waktu Mulai</th>
                                <th>Kategori</th>
                                {{-- <th>Foto</th> --}}
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $act)
                            @php $thumb = $act->photos->first(); @endphp
                            <tr>
                                <td>{{ $loop->iteration + ($activities->currentPage()-1)*$activities->perPage() }}</td>
                                <td>{{ \Carbon\Carbon::parse($act->activity_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($act->start_time)->format('H:i') }}</td>
                                <td>{{ $act->category->name ?? '-' }}</td>
                                {{-- <td class="text-center">
                                    @if($thumb)
                                    <img src="{{ asset('storage/'.$thumb->photo_url) }}" alt="Thumb"
                                        class="img-thumbnail" style="width:60px;height:60px;">
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td> --}}
                                <td>
                                    <span class="badge bg-{{ $act->status === 'Selesai' ? 'success' : 'warning' }}">
                                        {{ $act->status }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#detailActivityModal{{ $act->id }}">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada kegiatan lapangan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $activities->withQueryString()->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Kegiatan --}}
@foreach($activities as $act)
<div class="modal fade" id="detailActivityModal{{ $act->id }}" tabindex="-1"
    aria-labelledby="detailActivityLabel{{ $act->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailActivityLabel{{ $act->id }}">
                    Detail Kegiatan #{{ $act->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <table class="table table-borderless mb-3">
                    <tr>
                        <th style="width: 180px;">Tanggal</th>
                        <td>{{ \Carbon\Carbon::parse($act->activity_date)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Mulai</th>
                        <td>{{ \Carbon\Carbon::parse($act->start_time)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Selesai</th>
                        <td>{{ \Carbon\Carbon::parse($act->end_time)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $act->category->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $act->status === 'Selesai' ? 'success' : 'warning' }}">
                                {{ $act->status }}
                            </span>
                        </td>
                    </tr>
                    @if($act->description)
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $act->description }}</td>
                    </tr>
                    @endif
                </table>

                @if($act->photos->isNotEmpty())
                <h6 class="mb-2">Foto Kegiatan</h6>
                <div class="row g-2">
                    @foreach($act->photos as $photo)
                    <div class="col-md-3">
                        <img src="{{ asset('storage/'.$photo->photo_url) }}" alt="Foto" class="img-fluid rounded"
                            style="max-height:150px;">
                    </div>
                    @endforeach
                </div>
                @endif

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