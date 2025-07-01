<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Request as RequestModel;
use App\Models\RequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    // Tampilkan semua permintaan
    public function index()
    {
        $requests = RequestModel::with(['user', 'items', 'approver'])->orderByDesc('created_at')->paginate(10);
        return view('admin.requests.index', compact('requests'));
    }

    // Simpan permintaan baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_date'             => 'required|date',
            'status'                   => 'required|in:Pending,Approved,Rejected',
            'items'                    => 'required|array|min:1',
            'items.*.item_name'        => 'required|string|max:100',
            'items.*.item_type'        => 'nullable|string|max:50',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.unit'             => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $req = RequestModel::create([
                'user_id'      => Auth::id(),
                'request_date' => $validated['request_date'],
                'status'       => $validated['status'],
            ]);

            foreach ($validated['items'] as $item) {
                RequestItem::create([
                    'request_id' => $req->id,
                    'item_name'  => $item['item_name'],
                    'item_type'  => $item['item_type'],
                    'quantity'   => $item['quantity'],
                    'unit'       => $item['unit'],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.requests.index')->with('success', 'Permintaan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan permintaan.'])->withInput();
        }
    }

    // Approve permintaan
    public function approve(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        if ($req->status !== 'Pending') {
            return back()->with('error', 'Status permintaan sudah tidak bisa diubah.');
        }

        $req->status         = 'Approved';
        $req->approved_by    = Auth::id();
        $req->approved_at    = now();
        $req->rejected_reason = null;
        $req->save();

        return back()->with('success', 'Permintaan berhasil di-approve.');
    }

    // Reject permintaan
    public function reject(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        $request->validate([
            'rejected_reason' => 'required|string|max:255',
        ]);

        if ($req->status !== 'Pending') {
            return back()->with('error', 'Status permintaan sudah tidak bisa diubah.');
        }

        $req->status          = 'Rejected';
        $req->approved_by     = Auth::id();
        $req->approved_at     = now();
        $req->rejected_reason = $request->rejected_reason;
        $req->save();

        return back()->with('success', 'Permintaan berhasil direject.');
    }

    // Hapus permintaan beserta itemnya
    public function destroy($id)
    {
        $req = RequestModel::findOrFail($id);

        DB::beginTransaction();
        try {
            $req->items()->delete();
            $req->delete();
            DB::commit();
            return back()->with('success', 'Permintaan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus permintaan.');
        }
    }
}
