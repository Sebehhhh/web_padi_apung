{{-- resources/views/kepala/users/export_pdf.blade.php --}}
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
  <h2>Daftar Data Pegawai</h2>
  <table>
    <thead>
      <tr>
        <th style="width:4%">No</th>
        <th style="width:14%">NIP/NIK</th>
        <th style="width:18%">Nama</th>
        <th style="width:16%">Jabatan</th>
        <th style="width:16%">Divisi</th>
        <th style="width:10%">Status</th>
        <th>Alamat</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $i => $user)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $user->nip_nik }}</td>
          <td>{{ $user->name }}</td>
          <td>{{ $user->position }}</td>
          <td>{{ $user->division }}</td>
          <td>
            {{ $user->is_active ? 'Active' : 'Inactive' }}
          </td>
          <td>{{ $user->address }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <footer>
    Dicetak tanggal: {{ now()->format('d M Y H:i') }}
  </footer>
</body>
</html>