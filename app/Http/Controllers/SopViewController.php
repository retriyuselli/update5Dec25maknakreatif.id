<?php

namespace App\Http\Controllers;

use App\Models\Sop;
use App\Models\SopCategory;
use Illuminate\Http\Request;

class SopViewController extends Controller
{
    /**
     * Display a listing of SOPs for regular users
     */
    public function index(Request $request)
    {
        $query = Sop::with('category', 'creator')
            ->active()
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->search($search);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        $sops = $query->paginate(12);
        $categories = SopCategory::active()->orderBy('name')->get();

        return view('sop.index', compact('sops', 'categories'));
    }

    /**
     * Display the specified SOP
     */
    public function show($id)
    {
        $sop = Sop::with(['category', 'creator', 'updater'])
            ->active()
            ->findOrFail($id);

        return view('sop.show', compact('sop'));
    }

    /**
     * Search SOPs via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $sops = Sop::with('category')
            ->active()
            ->search($query)
            ->limit(10)
            ->get(['id', 'title', 'description', 'category_id']);

        return response()->json($sops);
    }

    /**
     * Get SOPs by category via AJAX
     */
    public function byCategory($categoryId)
    {
        $sops = Sop::with('category')
            ->active()
            ->byCategory($categoryId)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'description', 'version', 'created_at']);

        return response()->json($sops);
    }
}
