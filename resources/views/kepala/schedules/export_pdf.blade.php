<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Jadwal Kerja Harian</title>
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
    <h2>Laporan Jadwal Kerja Harian</h2>
    <table>
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:12%">Tanggal</th>
                <th style="width:18%">Pegawai</th>
                <th style="width:14%">Divisi</th>
                <th style="width:22%">Tugas</th>
                <th style="width:10%">Mulai</th>
                <th style="width:10%">Selesai</th>
                <th style="width:10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $i => $schedule)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') }}</td>
                <td>{{ $schedule->user->name ?? '-' }}</td>
                <td>{{ $schedule->user->division ?? '-' }}</td>
                <td>{{ $schedule->task }}</td>
                <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                <td>
                    <span class="badge bg-{{ $schedule->status === 'Selesai' ? 'success' : 'warning' }}">
                        {{ $schedule->status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#888;">Tidak ada data jadwal.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <footer>
        Dicetak tanggal: {{ now()->format('d M Y H:i') }}
    </footer>
</body>

</html>