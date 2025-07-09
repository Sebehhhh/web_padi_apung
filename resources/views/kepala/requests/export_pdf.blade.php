{{-- resources/views/kepala/requests/export_pdf.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .bg-warning {
            background: #ffe082;
            color: #795548;
        }

        .bg-success {
            background: #a5d6a7;
            color: #256029;
        }

        .bg-danger {
            background: #ef9a9a;
            color: #b71c1c;
        }

        .bg-secondary {
            background: #e0e0e0;
            color: #333;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 10px;
        }

        .sub-table th,
        .sub-table td {
            border: 1px solid #bbb;
            font-size: 11px;
        }

        .sub-table th {
            background: #f9f9f9;
        }
    </style>
</head>

<body>
    <h2>Laporan Permintaan Barang/Bahan</h2>
    <table>
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:13%">Tanggal</th>
                <th style="width:18%">Pemohon</th>
                <th style="width:10%">Status</th>
                <th style="width:18%">Disetujui Oleh</th>
                <th style="width:15%">Tanggal Approve</th>
                <th>Daftar Barang/Bahan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $i => $req)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($req->request_date)->format('d/m/Y') }}</td>
                <td>{{ $req->user->name ?? '-' }}</td>
                <td>
                    @php
                    $colors = ['Pending'=>'bg-warning','Approved'=>'bg-success','Rejected'=>'bg-danger'];
                    @endphp
                    <span class="badge {{ $colors[$req->status] ?? 'bg-secondary' }}">
                        {{ $req->status }}
                    </span>
                </td>
                <td>
                    @if($req->status !== 'Pending')
                    {{ $req->approver->name ?? '-' }}
                    @else
                    -
                    @endif
                </td>
                <td>
                    @if($req->status !== 'Pending')
                    {{ optional($req->approved_at)->format('d/m/Y H:i') }}
                    @else
                    -
                    @endif
                </td>
                <td>
                    <table class="sub-table" style="width:100%; margin:0;">
                        <thead>
                            <tr>
                                <th style="width:5%">No</th>
                                <th style="width:35%">Nama</th>
                                <th style="width:20%">Jenis</th>
                                <th style="width:15%">Jumlah</th>
                                <th style="width:15%">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($req->items as $j => $item)
                            <tr>
                                <td>{{ $j + 1 }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->item_type }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:#888;">Tidak ada item.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($req->status === 'Rejected' && $req->rejected_reason)
                    <div style="margin-top:4px; color:#b71c1c; font-size:11px;">
                        <strong>Alasan Tolak:</strong> {{ $req->rejected_reason }}
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <footer>
        Dicetak tanggal: {{ now()->format('d M Y H:i') }}
    </footer>
</body>

</html>