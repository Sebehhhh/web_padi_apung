@extends('layouts.app')
@section('title', 'Permintaan Barang/Bahan')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Permintaan Barang/Bahan</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRequestModal">
            Tambah Permintaan
          </button>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('pegawai.requests.index') }}" class="row g-3 align-items-end mt-4 mb-2">
          <div class="col-md-3">
            <label for="filter_status" class="form-label">Status</label>
            <select name="status" id="filter_status" class="form-select">
              <option value="">Semua</option>
              <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
              <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
              <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Periode Tanggal Permintaan</label>
            <div class="input-group">
              <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
              <span class="input-group-text">s/d</span>
              <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('pegawai.requests.index') }}" class="btn btn-secondary">Reset</a>
          </div>
        </form>
        {{-- End Filter --}}

        @if ($errors->any())
        <div class="alert alert-danger mt-3">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        @if(session('success'))
          <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif

        <div class="table-responsive mt-4">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($requests as $request)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($request->request_date ?? $request->created_at)->format('d M Y') }}</td>
                <td>
                  @php
                  $statusColor = [
                    'Pending' => 'warning',
                    'Approved' => 'success',
                    'Rejected' => 'danger'
                  ][$request->status] ?? 'secondary';
                  @endphp
                  <span class="badge bg-{{ $statusColor }}">
                    @if($request->status == 'Pending')
                      Menunggu
                    @elseif($request->status == 'Approved')
                      Disetujui
                    @elseif($request->status == 'Rejected')
                      Ditolak
                    @else
                      {{ ucfirst($request->status) }}
                    @endif
                  </span>
                </td>
                <td>
                  <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                    data-bs-target="#detailRequestModal{{ $request->id }}">Detail</button>
                  {{-- Pegawai tidak bisa edit/hapus jika status bukan Pending --}}
                  @if($request->status === 'Pending')
                  <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $request->id }})">Hapus</button>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center text-muted">Belum ada permintaan barang/bahan.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
          @if(method_exists($requests, 'links'))
            {{ $requests->appends(request()->query())->links('pagination::bootstrap-4') }}
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Tambah Permintaan --}}
<div class="modal fade" id="createRequestModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('pegawai.requests.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Permintaan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Tanggal Permintaan</label>
          <input type="date" name="request_date" class="form-control" required>
        </div>
        <hr>
        <label class="mb-2">Daftar Barang/Bahan</label>
        <table class="table table-bordered" id="itemsTable">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Jenis</th>
              <th>Jumlah</th>
              <th>Satuan</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><input type="text" name="items[0][item_name]" class="form-control" required></td>
              <td><input type="text" name="items[0][item_type]" class="form-control"></td>
              <td><input type="number" name="items[0][quantity]" class="form-control" min="1" required></td>
              <td><input type="text" name="items[0][unit]" class="form-control" required></td>
              <td><button type="button" class="btn btn-danger btn-sm remove-item-btn" style="display:none">&times;</button></td>
            </tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-outline-success" id="addItemBtn">
          <i class="ti ti-plus"></i> Tambah Barang/Bahan
        </button>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>
{{-- End Modal Tambah Permintaan --}}

{{-- Modal Detail Permintaan --}}
@foreach($requests as $request)
<div class="modal fade" id="detailRequestModal{{ $request->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success bg-opacity-10">
        <h5 class="modal-title">Detail Permintaan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <strong>Tanggal Permintaan:</strong>
          {{ \Carbon\Carbon::parse($request->request_date ?? $request->created_at)->format('d M Y') }}
        </div>
        <div class="mb-3">
          <strong>Status:</strong>
          @php
            $statusColor = [
              'Pending' => 'warning',
              'Approved' => 'success',
              'Rejected' => 'danger'
            ][$request->status] ?? 'secondary';
          @endphp
          <span class="badge bg-{{ $statusColor }}">
            @if($request->status == 'Pending')
              Menunggu
            @elseif($request->status == 'Approved')
              Disetujui
            @elseif($request->status == 'Rejected')
              Ditolak
            @else
              {{ ucfirst($request->status) }}
            @endif
          </span>
        </div>
        <div class="fw-bold mt-4 mb-2">Daftar Barang/Bahan</div>
        <table class="table table-bordered align-middle">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Jenis</th>
              <th>Jumlah</th>
              <th>Satuan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($request->items as $item)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $item->item_name }}</td>
              <td>{{ $item->item_type }}</td>
              <td>{{ $item->quantity }}</td>
              <td>{{ $item->unit }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center text-muted">Belum ada item.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endforeach
{{-- End Modal Detail Permintaan --}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function confirmDelete(requestId) {
    Swal.fire({
      title: 'Yakin ingin menghapus permintaan ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pegawai/requests/${requestId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  // Dinamis tambah item di modal tambah permintaan
  let itemIndex = 1;
  document.getElementById('addItemBtn').onclick = function() {
    const table = document.getElementById('itemsTable').querySelector('tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><input type="text" name="items[${itemIndex}][item_name]" class="form-control" required></td>
      <td><input type="text" name="items[${itemIndex}][item_type]" class="form-control"></td>
      <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" required></td>
      <td><input type="text" name="items[${itemIndex}][unit]" class="form-control" required></td>
      <td><button type="button" class="btn btn-danger btn-sm remove-item-btn">&times;</button></td>
    `;
    table.appendChild(row);
    itemIndex++;
  };

  document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-item-btn')) {
      e.target.closest('tr').remove();
    }
  });
</script>
@endsection
