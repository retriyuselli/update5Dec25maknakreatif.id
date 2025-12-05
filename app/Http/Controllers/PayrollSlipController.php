<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Support\Facades\Auth;

class PayrollSlipController extends Controller
{
    public function download(Payroll $record)
    {
        // Check authorization - only allow access to own payroll or super_admin
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthorized');
        }

        // If not super_admin, only allow access to own payroll
        if (! $user->roles->contains('name', 'super_admin') && $record->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        $user = $record->user;

        return view('payroll.slip-gaji-download', [
            'record' => $record,
            'user' => $user,
        ]);
    }
}
