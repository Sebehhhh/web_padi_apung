{{-- resources/views/kepala/harvests/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Hasil Panen')

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">

        <div class="d-md-flex align-items-center justify-content-between mb-3">
          <h4 class="card-title">Hasil Panen</h4>
        </div>

        {{-- FILTER --}}
        <form method="GET"
              action="{{ route('kepala.harvests.index') }}"
              class="row g-3 align-items-end mb-4">
          <div class="col-md-3">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date"
                   name="date_start"
                   class="form-control"
                   value="{{ request('date_start') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date"
                   name="date_end"
                   class="form-control"
                   value="{{ request('date_end') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Jenis Tanaman</label>
            <select name="crop_type_id" class="form-select">
              <option value="">Semua</option>
              @foreach($cropTypes as $crop)
                <option value="{{ $crop->id }}"
                  {{ request('crop_type_id') == $crop->id ? 'selected' : '' }}>
                  {{ $crop->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Kualitas</label>
            <select name="quality" class="form-select">
              <option value="">Semua</option>
              <option value="A" {{ request('quality')=='A'? 'selected':'' }}>A (Sangat Baik)</option>
              <option value="B" {{ request('quality')=='B'? 'selected':'' }}>B (Baik)</option>
              <option value="C" {{ request('quality')=='C'? 'selected':'' }}>C (Kurang)</option>
            </select>
          </div>
          <div class="col-md-1 text-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
          </div>
        </form>
        {{-- END FILTER --}}

        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal Panen</th>
                <th>Jenis Tanaman</th>
                <th>Luas (m²)</th>
                <th>Hasil (kg)</th>
                {{-- <th>Tonase</th>
                <th>Produktivitas<br>(kg/m²)</th> --}}
                <th>Kualitas</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($harvests as $harvest)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ \Carbon\Carbon::parse($harvest->harvest_date)->format('d M Y') }}</td>
                  <td>{{ $harvest->cropType->name ?? '-' }}</td>
                  <td>{{ number_format($harvest->land_area_m2, 2) }}</td>
                  <td>{{ number_format($harvest->total_weight_kg, 2) }}</td>
                  {{-- <td>{{ number_format($harvest->total_weight_ton, 4) }}</td>
                  <td>{{ number_format($harvest->productivity_kg_m2, 4) }}</td> --}}
                  <td>
                    @php
                      $color = $harvest->quality === 'A' ? 'success'
                              : ($harvest->quality === 'B' ? 'info' : 'danger');
                    @endphp
                    <span class="badge bg-{{ $color }}">
                      {{ $harvest->quality ?? '-' }}
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-info btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#detailHarvestModal{{ $harvest->id }}">
                      Detail
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center text-muted">
                    Belum ada data panen.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>

          {{ $harvests->withQueryString()->links('pagination::bootstrap-4') }}
        </div>

      </div>
    </div>
  </div>
</div>

{{-- Modal Detail Panen --}}
@foreach($harvests as $harvest)
<div class="modal fade" id="detailHarvestModal{{ $harvest->id }}" tabindex="-1" aria-labelledby="detailHarvestLabel{{ $harvest->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailHarvestLabel{{ $harvest->id }}">
          Detail Panen #{{ $harvest->id }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <table class="table table-borderless mb-3">
          <tr>
            <th style="width: 150px;">Tanggal Panen</th>
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
              <span class="badge bg-{{ $harvest->quality === 'A' ? 'success' : ($harvest->quality === 'B' ? 'info' : 'danger') }}">
                {{ $harvest->quality ?? '-' }}
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