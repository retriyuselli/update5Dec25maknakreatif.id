<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    public function create()
    {
        return view('prospect');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_event' => 'required|string|max:255',
            'date_lamaran' => 'nullable|date',
            'date_akad' => 'nullable|date',
            'date_resepsi' => 'nullable|date',
            'venue' => 'required|string|max:255',
            'name_cpp' => 'required|string|max:255',
            'name_cpw' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{8,15}$/',
            'address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Set user_id to null for public form submissions
        $validated['user_id'] = null;

        Prospect::create($validated);

        return redirect()->route('prospect.form')->with('success', 'Your prospect has been submitted.');
    }

    public function success()
    {
        return view('prospect-success');
    }
}
