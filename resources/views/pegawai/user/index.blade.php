@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <h1 class="mb-4 text-center">Profil Saya</h1>
            @if(session('success'))
                <div class="alert alert-success text-center">{{ session('success') }}</div>
            @endif

            <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #e0ffe7 0%, #f8fff8 100%);">
                <div class="card-body p-5 d-flex flex-column flex-md-row align-items-center">
                    <div class="me-md-5 mb-4 mb-md-0 text-center">
                        <div class="rounded-circle bg-success bg-opacity-25 d-flex align-items-center justify-content-center" style="width:120px; height:120px; margin:0 auto;">
                            <span style="font-size:3rem; color:#28a745;">
                                <i class="ti ti-user"></i>
                            </span>
                        </div>
                        <h4 class="mt-3 mb-0">{{ $user->name }}</h4>
                        <span class="badge bg-success mt-2" style="font-size:1rem;">{{ ucfirst($user->role) }}</span>
                    </div>
                    <div class="flex-fill">
                        <table class="table table-borderless mb-0" style="font-size:1.1rem;">
                            <tbody>
                                <tr>
                                    <th class="text-success" style="width: 150px;">Nama</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-success">Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th class="text-success">Role</th>
                                    <td>{{ ucfirst($user->role) }}</td>
                                </tr>
                                @if(isset($user->division))
                                <tr>
                                    <th class="text-success">Divisi</th>
                                    <td>{{ $user->division }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        {{-- Jika ingin menambah fitur edit profil, bisa tambahkan tombol di bawah --}}
                        {{-- <a href="{{ route('pegawai.users.edit', $user->id) }}" class="btn btn-warning mt-4">Edit Profil</a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
