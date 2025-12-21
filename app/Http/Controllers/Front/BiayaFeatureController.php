<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BiayaFeatureController extends Controller
{
    public function index()
    {
        return view('front.biaya_feature');
    }
}
