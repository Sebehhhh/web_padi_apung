{{-- resources/views/kepala/activities/export_pdf.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { border: 1px solid #444; padding: 6px 8px; }
    th { background-color: #f2f2f2; }
    h2 { text-align: center; margin-bottom: 0.5rem; }
    footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
  </style>
</head>
<body>
  <h2>Laporan Kegiatan Lapangan</h2>
  <table>
    <thead>
      <tr>
        <th style="width:4%">No</th>
        <th style="width:14%">Tanggal</th>
        <th style="width:10%">Mulai</th>
        <th style="width:10%">Selesai</th>
        <th style="width:20%">Kategori</th>
        <th style="width:10%">Status</th>
        <th>Deskripsi</th>
      </tr>
    </thead>
    <tbody>
      @foreach($activities as $i => $act)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ \Carbon\Carbon::parse($act->activity_date)->format('d/m/Y') }}</td>
          <td>{{ \Carbon\Carbon::parse($act->start_time)->format('H:i') }}</td>
          <td>{{ \Carbon\Carbon::parse($act->end_time)->format('H:i') }}</td>
          <td>{{ $act->category->name ?? '-' }}</td>
          <td>{{ $act->status }}</td>
          <td>{{ $act->description }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <footer>
    Dicetak tanggal: {{ now()->format('d M Y H:i') }}
  </footer>
</body>
</html>