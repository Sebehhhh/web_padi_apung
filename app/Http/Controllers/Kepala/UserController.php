<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of pegawai (read-only).
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        // Base query: hanya role 'pegawai'
        $query = User::where('role', 'pegawai');

        // Filter: cari di nama, nip_nik, atau jabatan (position)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhere('nip_nik', 'like', '%' . $q . '%')
                    ->orWhere('position', 'like', '%' . $q . '%');
            });
        }

        // Filter divisi
        if ($request->filled('division')) {
            $query->where('division', $request->division);
        }

        // Paginate & pertahankan query string
        $users = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Untuk dropdown filter divisi
        $divisions = User::where('role', 'pegawai')
            ->distinct()
            ->orderBy('division')
            ->pluck('division')
            ->filter()   // hilangkan null/empty
            ->values();

        return view('kepala.users.index', compact('users', 'divisions'));
    }
    public function export(Request $request)
    {
        // Base query: hanya role 'pegawai'
        $query = User::where('role', 'pegawai');

        // Filter: cari di nama, nip_nik, atau jabatan (position)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhere('nip_nik', 'like', '%' . $q . '%')
                    ->orWhere('position', 'like', '%' . $q . '%');
            });
        }

        // Filter divisi
        if ($request->filled('division')) {
            $query->where('division', $request->division);
        }

        $users = $query->orderBy('name')->get();
        $filename = 'pegawai_' . now()->format('Ymd_His') . '.pdf';

        // generate PDF dari view khusus
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kepala.users.export_pdf', compact('users'))
            ->setPaper('a4', 'potrait');

        return $pdf->download($filename);
    }

    /**
     * Tidak tersedia untuk Kepala (read-only).
     */
    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show($id)
    {
        abort(404);
    }

    public function edit($id)
    {
        abort(404);
    }

    public function update(Request $request, $id)
    {
        abort(404);
    }

    public function destroy($id)
    {
        abort(404);
    }
}
