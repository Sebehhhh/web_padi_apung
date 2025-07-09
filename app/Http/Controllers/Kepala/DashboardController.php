<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Dashboard Kepala.
     */
    public function index()
    {
        // Langsung ke view ‘kepala.dashboard’
        return view('kepala.dashboard');
    }
}