<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as RequestModel;
use App\Models\RequestItem;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    /**
     * Tampilkan daftar permintaan milik pegawai yang sedang login, dengan filter sederhana.
     * Form create permintaan & detail permintaan via modal di halaman index.
     */
    public function index(Request $request)
    {
        $query = RequestModel::with(['items'])
            ->where('user_id', Auth::id());

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter tanggal permintaan (range)
        if ($request->filled('start_date')) {
            $query->whereDate('request_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('request_date', '<=', $request->input('end_date'));
        }

        $requests = $query->orderByDesc('created_at')->get();

        return view('pegawai.requests.index', compact('requests'));
    }

    /**
     * Simpan permintaan baru (hanya untuk pegawai sendiri).
     * Form input berasal dari modal di halaman index.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_date'             => 'required|date',
            'items'                    => 'required|array|min:1',
            'items.*.item_name'        => 'required|string|max:100',
            'items.*.item_type'        => 'nullable|string|max:50',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.unit'             => 'required|string|max:20',
        ], [
            'items.required' => 'Minimal 1 barang/bahan harus diisi.',
            'items.*.item_name.required' => 'Nama barang/bahan wajib diisi.',
            'items.*.quantity.required' => 'Jumlah barang/bahan wajib diisi.',
            'items.*.unit.required' => 'Satuan barang/bahan wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $permintaan = RequestModel::create([
                'user_id'      => Auth::id(),
                'request_date' => $validated['request_date'],
                'status'       => 'Pending',
            ]);

            foreach ($validated['items'] as $item) {
                RequestItem::create([
                    'request_id' => $permintaan->id,
                    'item_name'  => $item['item_name'],
                    'item_type'  => $item['item_type'] ?? null,
                    'quantity'   => $item['quantity'],
                    'unit'       => $item['unit'],
                ]);
            }

            DB::commit();
            return redirect()->route('pegawai.requests.index')
                ->with('success', 'Permintaan berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan permintaan.']);
        }
    }

    // Tidak ada method show karena detail permintaan ditampilkan via modal di halaman index
}
