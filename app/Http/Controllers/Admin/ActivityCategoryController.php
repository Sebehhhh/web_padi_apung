<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class ActivityCategoryController extends Controller
{
    // Tampilkan semua kategori (dengan pagination)
    public function index()
    {
        $categories = ActivityCategory::orderBy('name')->paginate(10);
        return view('admin.activity_categories.index', compact('categories'));
    }

    // Simpan kategori baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:50|unique:activity_categories,name',
            'description' => 'nullable|string|max:100',
            'is_active'   => 'required|boolean',
        ]);
        $category = ActivityCategory::create($validated);

        // Logging
        ActivityLogger::log(
            'create',
            'activity_category',
            'Tambah kategori: ' . $category->name . ' oleh user: ' . Auth::user()->name
        );

        return redirect()->route('admin.activity-categories.index')->with('success', 'Kategori berhasil ditambah.');
    }

    // Update kategori
    public function update(Request $request, ActivityCategory $activity_category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:50|unique:activity_categories,name,' . $activity_category->id,
            'description' => 'nullable|string|max:100',
            'is_active'   => 'required|boolean',
        ]);
        $activity_category->update($validated);

        // Logging
        ActivityLogger::log(
            'update',
            'activity_category',
            'Update kategori: ' . $activity_category->name . ' oleh user: ' . Auth::user()->name
        );

        return redirect()->route('admin.activity-categories.index')->with('success', 'Kategori berhasil diupdate.');
    }

    // Hapus kategori
    public function destroy(ActivityCategory $activity_category)
    {
        $categoryName = $activity_category->name;
        $activity_category->delete();

        // Logging
        ActivityLogger::log(
            'delete',
            'activity_category',
            'Hapus kategori: ' . $categoryName . ' oleh user: ' . Auth::user()->name
        );

        return redirect()->route('admin.activity-categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}