<?php

namespace App\Http\Controllers;

class UserFormPdfController extends Controller
{
    /**
     * Display blank user registration form
     */
    public function generateBlankForm()
    {
        $data = [
            'title' => 'FORMULIR PENDATAAN KARYAWAN',
            'company' => 'PT. Makna Kreatif Indonesia',
            'generated_date' => now()->format('d F Y'),
            'form_number' => 'FRM-HR-'.now()->format('Ymd').'-'.str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
        ];

        return view('pdf.user-registration-form', $data);
    }
}
