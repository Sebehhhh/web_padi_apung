<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Request as RequestModel;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;

class RequestController extends Controller
{
    /**
     * Tampilkan ringkasan & histori permintaan tim.
     */
    public function index(HttpRequest $request)
    {
        $query = RequestModel::with(['user', 'items', 'approver']);

        // filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('request_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('request_date', '<=', $request->end_date);
        }
        // filter pemohon
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $requests = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $users = User::orderBy('name')->get();

        return view('kepala.requests.index', [
            'requests' => $requests,
            'users'    => $users,
        ]);
    }

    /**
     * Tampilkan detail satu permintaan.
     *
     * @param  \App\Models\Request  $request  (wildcard {request})
     */
   
}