<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function logo()
    {
        $company = Schema::hasTable('companies') ? Company::query()->first() : null;
        $path = public_path('images/logomki.png');
        if ($company && $company->logo_url && Storage::disk('public')->exists($company->logo_url)) {
            $path = Storage::disk('public')->path($company->logo_url);
        }

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function favicon()
    {
        $company = Schema::hasTable('companies') ? Company::query()->first() : null;
        $path = public_path('images/favicon_makna.png');
        if ($company && $company->favicon_url && Storage::disk('public')->exists($company->favicon_url)) {
            $path = Storage::disk('public')->path($company->favicon_url);
        }

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function loginImage()
    {
        $company = Schema::hasTable('companies') ? Company::query()->first() : null;
        $path = public_path('images/team_makna.jpg');
        if ($company && $company->image_login && Storage::disk('public')->exists($company->image_login)) {
            $path = Storage::disk('public')->path($company->image_login);
        }

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
