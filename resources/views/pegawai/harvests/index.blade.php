@extends('layouts.app')
@section('title', 'Catatan Panen Saya')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Catatan Panen Saya</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHarvestModal">
            Tambah Panen
          </button>
        </div>

        {{-- FILTER --}}
        <form method="GET" action="{{ route('pegawai.harvests.index') }}" class="row g-2 my-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="date_start" class="form-control" value="{{ request('date_start') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" name="date_end" class="form-control" value="{{ request('date_end') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Jenis Tanaman</label>
            <select name="crop_type_id" class="form-select">
              <option value="">Semua</option>
              @foreach($cropTypes as $crop)
              <option value="{{ $crop->id }}" {{ request('crop_type_id')==$crop->id ? 'selected' : '' }}>
                {{ $crop->name }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Kualitas</label>
            <select name="quality" class="form-select">
              <option value="">Semua</option>
              <option value="A" {{ request('quality')=='A' ? 'selected' :'' }}>A (Sangat Baik)</option>
              <option value="B" {{ request('quality')=='B' ? 'selected' :'' }}>B (Baik)</option>
              <option value="C" {{ request('quality')=='C' ? 'selected' :'' }}>C (Kurang)</option>
            </select>
          </div>
          <div class="col-md-1 text-end">
            <button type="submit" class="btn btn-secondary w-100">Filter</button>
          </div>
        </form>
        {{-- END FILTER --}}

        @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger mt-3">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <div class="table-responsive mt-4">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal Panen</th>
                <th>Jenis Tanaman</th>
                <th>Luas (m²)</th>
                <th>Hasil (kg)</th>
                <th>Tonase</th>
                <th>Produktivitas<br>(kg/m²)</th>
                <th>Kualitas</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($harvests as $harvest)
              <tr>
                <td>{{ ($harvests->currentPage() - 1) * $harvests->perPage() + $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($harvest->harvest_date)->format('d M Y') }}</td>
                <td>{{ $harvest->cropType->name ?? '-' }}</td>
                <td>{{ number_format($harvest->land_area_m2, 2) }}</td>
                <td>{{ number_format($harvest->total_weight_kg, 2) }}</td>
                <td>{{ number_format($harvest->total_weight_ton, 4) }}</td>
                <td>{{ number_format($harvest->productivity_kg_m2, 4) }}</td>
                <td>
                  <span
                    class="badge bg-{{ $harvest->quality == 'A' ? 'success' : ($harvest->quality == 'B' ? 'info' : 'danger') }}">
                    {{ $harvest->quality ?? '-' }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                    data-bs-target="#detailHarvestModal{{ $harvest->id }}">Detail</button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center text-muted">Belum ada data panen.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
          {{ $harvests->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Detail --}}
@foreach($harvests as $harvest)
<div class="modal fade" id="detailHarvestModal{{ $harvest->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Panen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless">
          <tr>
            <th>Tanggal Panen</th>
            <td>{{ \Carbon\Carbon::parse($harvest->harvest_date)->format('d M Y') }}</td>
          </tr>
          <tr>
            <th>Jenis Tanaman</th>
            <td>{{ $harvest->cropType->name ?? '-' }}</td>
          </tr>
          <tr>
            <th>Luas Lahan</th>
            <td>{{ number_format($harvest->land_area_m2, 2) }} m²</td>
          </tr>
          <tr>
            <th>Total Hasil</th>
            <td>{{ number_format($harvest->total_weight_kg, 2) }} kg</td>
          </tr>
          <tr>
            <th>Tonase</th>
            <td>{{ number_format($harvest->total_weight_ton, 4) }} ton</td>
          </tr>
          <tr>
            <th>Produktivitas</th>
            <td>{{ number_format($harvest->productivity_kg_m2, 4) }} kg/m²</td>
          </tr>
          <tr>
            <th>Kualitas</th>
            <td>
              <span
                class="badge bg-{{ $harvest->quality == 'A' ? 'success' : ($harvest->quality == 'B' ? 'info' : 'danger') }}">
                {{ $harvest->quality ?? '-' }}
              </span>
            </td>
          </tr>
          <tr>
            <th>Catatan</th>
            <td>{{ $harvest->notes ?? '-' }}</td>
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

{{-- Modal Tambah Panen --}}
<div class="modal fade" id="createHarvestModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('pegawai.harvests.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Panen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Tanggal Panen</label>
          <input type="date" name="harvest_date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Jenis Tanaman</label>
          <select name="crop_type_id" class="form-select" required>
            @foreach($cropTypes as $crop)
            <option value="{{ $crop->id }}">{{ $crop->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Luas Lahan (m²)</label>
          <input type="number" name="land_area_m2" class="form-control" min="1" step="0.01" required>
        </div>
        <div class="mb-3">
          <label>Total Hasil (kg)</label>
          <input type="number" name="total_weight_kg" class="form-control" min="1" step="0.01" required>
        </div>
        <div class="mb-3">
          <label>Catatan</label>
          <input type="text" name="notes" class="form-control" maxlength="255">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection
