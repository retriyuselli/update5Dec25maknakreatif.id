<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanFeatureController extends Controller
{
    public function index()
    {
        return view('front.laporan_feature');
    }
}
