 {{-- resources/views/kepala/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">

                <div class="d-md-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title">Data Pegawai</h4>
                </div>

                {{-- FILTER + EXPORT --}}
                <form method="GET" action="{{ route('kepala.users.index') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Cari Nama / NIP/NIK / Jabatan</label>
                        <input type="text" name="q" class="form-control" placeholder="Ketik nama, NIP/NIK, atau jabatan..."
                            value="{{ request('q') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Divisi</label>
                        <select name="division" class="form-select">
                            <option value="">Semua Divisi</option>
                            @foreach($divisions as $div)
                            <option value="{{ $div }}" {{ request('division')==$div ? 'selected' : '' }}>
                                {{ $div }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('kepala.users.export', request()->only(['q','division'])) }}"
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
                                <th>NIP/NIK</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Divisi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage()-1)*$users->perPage() }}</td>
                                <td>{{ $user->nip_nik }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->position }}</td>
                                <td>{{ $user->division }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#detailUserModal{{ $user->id }}">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Tidak ada pegawai.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $users->withQueryString()->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Pegawai --}}
@foreach($users as $user)
<div class="modal fade" id="detailUserModal{{ $user->id }}" tabindex="-1"
    aria-labelledby="detailUserLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailUserLabel{{ $user->id }}">
                    Profil Pegawai: {{ $user->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width: 180px;">NIP/NIK</th>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection