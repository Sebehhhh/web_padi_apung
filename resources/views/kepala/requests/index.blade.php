{{-- resources/views/kepala/requests/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Permintaan Barang/Bahan')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">

                <div class="d-md-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title">Permintaan Barang/Bahan</h4>
                </div>

                {{-- FILTER --}}
                <form method="GET" action="{{ route('kepala.requests.index') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="Pending" {{ request('status')=='Pending' ? 'selected' :'' }}>Pending</option>
                            <option value="Approved" {{ request('status')=='Approved' ? 'selected' :'' }}>Approved
                            </option>
                            <option value="Rejected" {{ request('status')=='Rejected' ? 'selected' :'' }}>Rejected
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pemohon</label>
                        <select name="user_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id')==$user->id ? 'selected':'' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Periode Tanggal</label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                            <span class="input-group-text">s/d</span>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('kepala.requests.index') }}" class="btn btn-secondary w-100 mt-1">Reset</a>
                    </div>
                </form>
                {{-- END FILTER --}}

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pemohon</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($req->request_date)->format('d M Y') }}</td>
                                <td>{{ $req->user->name }}</td>
                                <td>
                                    @php
                                    $colors = ['Pending'=>'warning','Approved'=>'success','Rejected'=>'danger'];
                                    @endphp
                                    <span class="badge bg-{{ $colors[$req->status] ?? 'secondary' }}">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#detailModal{{ $req->id }}">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada permintaan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $requests->withQueryString()->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Detail untuk setiap permintaan --}}
@foreach($requests as $req)
<div class="modal fade" id="detailModal{{ $req->id }}" tabindex="-1" aria-labelledby="detailLabel{{ $req->id }}"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailLabel{{ $req->id }}">Detail Permintaan #{{ $req->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <table class="table table-borderless mb-3">
                    <tr>
                        <th style="width: 150px;">Tanggal</th>
                        <td>{{ \Carbon\Carbon::parse($req->request_date)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Pemohon</th>
                        <td>{{ $req->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        @php
                        $colors = ['Pending'=>'warning','Approved'=>'success','Rejected'=>'danger'];
                        @endphp
                        <td>
                            <span class="badge bg-{{ $colors[$req->status] ?? 'secondary' }}">
                                {{ $req->status }}
                            </span>
                        </td>
                    </tr>
                    @if($req->status !== 'Pending')
                    <tr>
                        <th>Disetujui Oleh</th>
                        <td>{{ $req->approver->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Pada</th>
                        <td>{{ optional($req->approved_at)->format('d M Y H:i') }}</td>
                    </tr>
                    @if($req->status === 'Rejected')
                    <tr>
                        <th>Alasan Tolak</th>
                        <td>{{ $req->rejected_reason }}</td>
                    </tr>
                    @endif
                    @endif
                </table>

                <h6 class="mb-2">Daftar Barang/Bahan</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th style="width: 80px;">Jumlah</th>
                                <th style="width: 100px;">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($req->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->item_type }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada item.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection