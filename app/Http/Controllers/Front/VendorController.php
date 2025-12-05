<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua kategori untuk dropdown filter
        $categories = Category::all();

        // Ambil parameter per_page (default 10)
        $perPage = $request->get('per_page', 10);

        // Query builder untuk vendor dengan relasi category
        $query = Vendor::with('category');

        // Filter berdasarkan pencarian nama
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('pic_name', 'like', '%'.$request->search.'%')
                ->orWhere('phone', 'like', '%'.$request->search.'%');
        }

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Gunakan pagination instead of get()
        $vendors = $query->paginate($perPage)->appends($request->query());

        // Untuk statistik, kita perlu query terpisah tanpa pagination
        $allVendorsQuery = Vendor::query();

        // Apply same filters for stats
        if ($request->filled('search')) {
            $allVendorsQuery->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('pic_name', 'like', '%'.$request->search.'%')
                ->orWhere('phone', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('category')) {
            $allVendorsQuery->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $allVendorsQuery->where('status', $request->status);
        }

        $allVendors = $allVendorsQuery->get();

        // Filter vendor hanya untuk tahun berjalan (berdasarkan created_at) untuk perhitungan revenue
        $currentYearVendors = $allVendors->filter(function ($vendor) {
            return $vendor->created_at && $vendor->created_at->year == date('Y');
        });

        // Hitung total harga publish untuk estimasi revenue (tahun berjalan)
        $totalRevenue = $currentYearVendors->sum('harga_publish');
        $totalVendorCost = $currentYearVendors->sum('harga_vendor');
        $estimatedProfit = $totalRevenue - $totalVendorCost;

        // Statistik vendor yang lebih detail
        $stats = [
            'total' => $allVendors->count(),
            'active' => $allVendors->where('status', 'vendor')->count(),
            'pending' => $allVendors->where('status', 'product')->count(),
            'revenue' => 'Rp '.number_format($totalRevenue, 0, ',', '.'),
            'profit' => 'Rp '.number_format($estimatedProfit, 0, ',', '.'),
            'average_price' => $allVendors->count() > 0 ? 'Rp '.number_format($allVendors->avg('harga_publish'), 0, ',', '.') : 'Rp 0',
            'current_year' => date('Y'),
        ];

        return view('front.vendor', compact('vendors', 'stats', 'categories'));
    }
}
