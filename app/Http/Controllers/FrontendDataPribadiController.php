<?php

namespace App\Http\Controllers;

use App\Models\DataPribadi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View; // Import Validator

class FrontendDataPribadiController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('data-pribadi.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:data_pribadis,email',
            'nomor_telepon' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            // Validasi untuk foto: harus gambar, tipe mime tertentu, dan ukuran maksimal 1MB (1024 KB)
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'pekerjaan' => 'nullable|string|max:255',
            'gaji' => 'nullable|numeric|min:0', // Pastikan ini sudah dibersihkan dari format titik jika perlu
            'motivasi_kerja' => 'nullable|string',
            'pelatihan' => 'nullable|string',
        ]);

        // Membersihkan input gaji dari format titik sebelum validasi jika dikirim dengan format
        if ($request->has('gaji')) {
            $request->merge([
                'gaji' => str_replace('.', '', $request->input('gaji')),
            ]);
        }

        if ($validator->fails()) {
            return redirect()->route('data-pribadi.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('data-pribadi-fotos', 'public');
            $data['foto'] = $path;
        }

        DataPribadi::create($data);

        return redirect()->route('data-pribadi.index')->with('success', 'Data pribadi berhasil disimpan!');
    }

    public function index(Request $request) // Tambahkan Request $request
    {
        $query = DataPribadi::query();

        // Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            // Sesuaikan 'nama_lengkap' dengan nama kolom yang benar di tabel Anda
            $query->where('nama_lengkap', 'LIKE', '%'.$searchTerm.'%');
        }

        $dataPribadis = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('data-pribadi.index', compact('dataPribadis'));
    }
}
