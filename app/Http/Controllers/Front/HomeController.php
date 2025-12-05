<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CompanyLogo;

class HomeController extends Controller
{
    public function index()
    {
        // Get active company logos for homepage display
        $clientLogos = CompanyLogo::active()
            ->category('client')
            ->ordered()
            ->take(4)
            ->get();

        $partnerLogos = CompanyLogo::active()
            ->category('partner')
            ->ordered()
            ->take(4)
            ->get();

        $vendorLogos = CompanyLogo::active()
            ->category('vendor')
            ->ordered()
            ->take(4)
            ->get();

        $sponsorLogos = CompanyLogo::active()
            ->category('sponsor')
            ->ordered()
            ->take(4)
            ->get();

        // Combine all logos for single row display
        $allLogos = collect()
            ->merge($clientLogos)
            ->merge($partnerLogos)
            ->merge($vendorLogos)
            ->merge($sponsorLogos)
            ->shuffle();

        // Single row with all logos
        $topRowLogos = $allLogos->take(12);
        $bottomRowLogos = collect(); // Empty collection for backward compatibility

        return view('front.home', compact(
            'topRowLogos',
            'bottomRowLogos',
            'clientLogos',
            'partnerLogos',
            'vendorLogos',
            'sponsorLogos'
        ));
    }
}
