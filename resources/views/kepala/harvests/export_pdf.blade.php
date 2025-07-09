<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Panen</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin-bottom: 18px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 16px;
        }

        th,
        td {
            border: 1px solid #bbb;
            padding: 6px 8px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background: #f5f5f5;
        }

        .bg-success {
            background: #a5d6a7;
            color: #256029;
        }

        .bg-warning {
            background: #ffe082;
            color: #795548;
        }

        .bg-danger {
            background: #ef9a9a;
            color: #b71c1c;
        }

        .bg-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <h2>Laporan Hasil Panen</h2>
     <table>
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:12%">Tanggal</th>
                <th style="width:16%">Petugas</th>
                <th style="width:16%">Jenis Tanaman</th>
                <th style="width:10%">Luas Lahan (m²)</th>
                <th style="width:12%">Total Hasil (kg)</th>
                <th style="width:10%">Tonase (ton)</th>
                <th style="width:12%">Produktivitas (kg/m²)</th>
                <th style="width:8%">Kualitas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($harvests as $i => $harvest)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($harvest->harvest_date)->format('d/m/Y') }}</td>
                <td>{{ $harvest->user->name ?? '-' }}</td>
                <td>{{ $harvest->cropType->name ?? '-' }}</td>
                <td>{{ number_format($harvest->land_area_m2, 2) }}</td>
                <td>{{ number_format($harvest->total_weight_kg, 2) }}</td>
                <td>{{ number_format($harvest->total_weight_ton, 4) }}</td>
                <td>{{ number_format($harvest->productivity_kg_m2, 4) }}</td>
                <td>
                    <span class="badge bg-{{ $harvest->quality === 'A' ? 'success' : ($harvest->quality === 'B' ? 'warning' : 'danger') }}">
                        {{ $harvest->quality ?? '-' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; color:#888;">Tidak ada data hasil panen.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <footer>
        Dicetak tanggal: {{ now()->format('d M Y H:i') }}
    </footer>
</body>

</html>