<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\ProspectApp;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProspectAppController extends Controller
{
    /**
     * Display the prospect application form.
     */
    public function create()
    {
        $industries = Industry::orderBy('industry_name')->get();

        return view('prospect-app.form-tailwind', compact('industries'));
    }

    /**
     * Display the comprehensive prospect registration form.
     */
    public function pendaftaran()
    {
        $industries = Industry::where('is_active', true)->orderBy('industry_name')->get();

        return view('front.pendaftaran', compact('industries'));
    }

    /**
     * Store a newly created prospect application.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:prospect_apps,email',
            'phone' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'company_name' => 'required|string|max:255',
            'industry_id' => 'required|exists:industries,id',
            'name_of_website' => 'nullable|string|max:255',
            'user_size' => 'required|in:1-10,11-50,51-200,201-500,501-1000,1000+',
            'service' => 'nullable|in:basic,standard,premium,enterprise',
            'reason_for_interest' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar sebelumnya.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'industry_id.required' => 'Industri wajib dipilih.',
            'industry_id.exists' => 'Industri yang dipilih tidak valid.',
            'user_size.required' => 'Ukuran perusahaan wajib dipilih.',
            'user_size.in' => 'Ukuran perusahaan tidak valid.',
            'service.in' => 'Paket layanan tidak valid.',
            'reason_for_interest.required' => 'Alasan ketertarikan wajib diisi.',
            'reason_for_interest.max' => 'Alasan minat maksimal 1000 karakter.',
            'notes.max' => 'Catatan tambahan maksimal 2000 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create new prospect application
            $prospect = ProspectApp::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'position' => $request->position,
                'company_name' => $request->company_name,
                'industry_id' => $request->industry_id,
                'name_of_website' => $request->name_of_website,
                'user_size' => $request->user_size,
                'service' => $request->service,
                'reason_for_interest' => $request->reason_for_interest,
                'notes' => $request->notes,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            // Send notification email (optional)
            try {
                $this->sendNotificationEmail($prospect);
            } catch (Exception $e) {
                // Log email error but don't fail the whole process
                Log::error('Failed to send prospect notification email: '.$e->getMessage());
            }

            return redirect()->route('prospect-app.success')->with('success',
                'Terima kasih! Aplikasi Anda telah berhasil dikirim. Tim kami akan menghubungi Anda dalam 24 jam.'
            );

        } catch (Exception $e) {
            Log::error('Failed to create prospect application: '.$e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])
                ->withInput();
        }
    }

    /**
     * Send notification email to admin and prospect
     */
    private function sendNotificationEmail(ProspectApp $prospect)
    {
        // Send to admin
        if (config('mail.admin_email')) {
            Mail::send('emails.prospect-app.admin-notification', compact('prospect'), function ($message) {
                $message->to(config('mail.admin_email'))
                    ->subject('New Prospect Application - '.config('app.name'));
            });
        }

        // Send confirmation to prospect
        Mail::send('emails.prospect-app.confirmation', compact('prospect'), function ($message) use ($prospect) {
            $message->to($prospect->email, $prospect->full_name)
                ->subject('Konfirmasi Aplikasi Prospek - '.config('app.name'));
        });
    }

    /**
     * Show success page
     */
    public function success()
    {
        return view('prospect-app.success-tailwind');
    }

    /**
     * Check if email already exists (for AJAX validation)
     */
    public function checkEmail(Request $request)
    {
        $exists = ProspectApp::where('email', $request->email)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Email sudah terdaftar sebelumnya.' : 'Email tersedia.',
        ]);
    }

    /**
     * Generate PDF proposal for prospect application.
     */
    public function generatePdf($id)
    {
        $prospect = ProspectApp::findOrFail($id);

        $pdf = Pdf::loadView('prospect-app.proposal-pdf', compact('prospect'));

        return $pdf->download('proposal_prospect_'.$prospect->id.'.pdf');
    }

    /**
     * Generate PDF proposal for prospect application
     */
    public function generateProposalPdf(ProspectApp $prospectApp)
    {
        try {
            // Load prospect app with industry relationship
            $prospectApp->load('industry');

            // Generate PDF
            $pdf = Pdf::loadView('invoices.prospectapp', compact('prospectApp'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

            $filename = 'invoice-'.$prospectApp->company_name.'-'.$prospectApp->id.'.pdf';

            return $pdf->download($filename);

        } catch (Exception $e) {
            Log::error('PDF Generation Error: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal generate proposal PDF: '.$e->getMessage());
        }
    }
}
