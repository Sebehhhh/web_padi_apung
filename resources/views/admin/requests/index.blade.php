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
        <form method="GET" action="{{ route('admin.requests.index') }}" class="row g-3 align-items-end mt-4 mb-2">
          <div class="col-md-3">
            <label for="filter_status" class="form-label">Status</label>
            <select name="status" id="filter_status" class="form-select">
              <option value="">Semua</option>
              <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
              <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
              <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="filter_user" class="form-label">Pemohon</label>
            <select name="user_id" id="filter_user" class="form-select">
              <option value="">Semua</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                  {{ $user->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Periode Tanggal Permintaan</label>
            <div class="input-group">
              <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
              <span class="input-group-text">s/d</span>
              <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.requests.index') }}" class="btn btn-secondary">Reset</a>
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
        <div class="table-responsive mt-4">
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
              @forelse($requests as $request)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($request->request_date)->format('d M Y') }}</td>
                <td>{{ $request->user->name ?? '-' }}</td>
                <td>
                  @php
                  $statusColor = [
                  'Pending' => 'warning',
                  'Approved' => 'success',
                  'Rejected' => 'danger'
                  ][$request->status] ?? 'secondary';
                  @endphp
                  <span class="badge bg-{{ $statusColor }}">{{ $request->status }}</span>
                </td>
                <td>
                  <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                    data-bs-target="#detailRequestModal{{ $request->id }}">Detail</button>
                  @if($request->status === 'Pending')
                  <button class="btn btn-success btn-sm" onclick="approveRequest({{ $request->id }})">Approve</button>
                  <button class="btn btn-danger btn-sm" onclick="rejectRequest({{ $request->id }})">Reject</button>
                  @endif
                  <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $request->id }})">Hapus</button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center text-muted">Belum ada permintaan barang/bahan.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
          {{ $requests->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Tambah Permintaan --}}
<div class="modal fade" id="createRequestModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.requests.store') }}">
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
        <div class="mb-3">
          <label>Status</label>
          <select name="status" class="form-control" required>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
          </select>
        </div>
        <hr>
        <label class="mb-2">Daftar Barang/Bahan</label>
        <table class="table table-bordered" id="itemsTable">
          <thead>
            <tr>
              <th>Nama Barang/Bahan</th>
              <th>Jenis</th>
              <th>Jumlah</th>
              <th>Satuan</th>
              <th><button type="button" class="btn btn-success btn-sm" id="addItemBtn">+</button></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><input type="text" name="items[0][item_name]" class="form-control" required></td>
              <td><input type="text" name="items[0][item_type]" class="form-control"></td>
              <td><input type="number" name="items[0][quantity]" class="form-control" min="1" required></td>
              <td><input type="text" name="items[0][unit]" class="form-control" required></td>
              <td></td>
            </tr>
          </tbody>
        </table>
        <div class="text-muted small">Klik + untuk menambah baris barang/bahan.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Detail --}}
@foreach($requests as $request)
<div class="modal fade" id="detailRequestModal{{ $request->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Permintaan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless">
          <tr>
            <th>Tanggal</th>
            <td>{{ \Carbon\Carbon::parse($request->request_date)->format('d M Y') }}</td>
          </tr>
          <tr>
            <th>Pemohon</th>
            <td>{{ $request->user->name ?? '-' }}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>
              @php
              $statusColor = [
              'Pending' => 'warning',
              'Approved' => 'success',
              'Rejected' => 'danger'
              ][$request->status] ?? 'secondary';
              @endphp
              <span class="badge bg-{{ $statusColor }}">
                {{ $request->status }}
              </span>
            </td>
          </tr>
          @if($request->approved_by)
          <tr>
            <th>Disetujui Oleh</th>
            <td>{{ $request->approver->name ?? '-' }}</td>
          </tr>
          <tr>
            <th>Disetujui Pada</th>
            <td>{{ $request->approved_at ? \Carbon\Carbon::parse($request->approved_at)->format('d M Y H:i') : '-' }}
            </td>
          </tr>
          @endif
          @if($request->rejected_reason)
          <tr>
            <th>Alasan Ditolak</th>
            <td>{{ urldecode($request->rejected_reason) }}</td>
          </tr>
          @endif
        </table>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Approve permintaan
  function approveRequest(requestId) {
    Swal.fire({
      title: 'Approve permintaan?',
      text: 'Yakin ingin approve permintaan ini?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Approve'
    }).then((result) => {
      if (result.isConfirmed) {
        // Submit form approve via JS
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/requests/${requestId}/approve`;
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  // Reject permintaan
  function rejectRequest(requestId) {
    Swal.fire({
      title: 'Tolak permintaan',
      input: 'textarea',
      inputLabel: 'Alasan Penolakan',
      inputPlaceholder: 'Tuliskan alasan penolakan...',
      inputAttributes: { maxlength: 255, required: true },
      showCancelButton: true,
      confirmButtonText: 'Tolak',
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      preConfirm: (reason) => {
        if (!reason) {
          Swal.showValidationMessage('Alasan penolakan wajib diisi');
        }
        return reason;
      }
    }).then((result) => {
      if (result.isConfirmed && result.value) {
        // Submit form reject via JS
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/requests/${requestId}/reject`;
        form.innerHTML = '@csrf' +
          `<input type="hidden" name="rejected_reason" value="${encodeURIComponent(result.value)}">`;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

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
        form.action = `/admin/requests/${requestId}`;
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