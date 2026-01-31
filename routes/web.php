<?php

use App\Http\Controllers\BankReconciliationTemplateController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\BiayaFeatureController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\HrisFeatureController;
use App\Http\Controllers\Front\InvoiceController as FrontInvoiceController;
use App\Http\Controllers\Front\LaporanFeatureController;
use App\Http\Controllers\Front\PayrollFeatureController;
use App\Http\Controllers\Front\AsetFeatureController;
use App\Http\Controllers\FrontendDataPribadiController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvoiceOrderController;
use App\Http\Controllers\ProductDisplayController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SimulasiDisplayController;
use App\Http\Controllers\UserFormPdfController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\DataPembayaran; // Add for debug

// Bank Reconciliation Template Route
Route::get('/bank-reconciliation/template', [BankReconciliationTemplateController::class, 'downloadTemplate'])
    ->name('bank-reconciliation.template');

Route::get('/brand/logo', [BrandController::class, 'logo'])->name('brand.logo');
Route::get('/brand/favicon', [BrandController::class, 'favicon'])->name('brand.favicon');
Route::get('/brand/login-image', [BrandController::class, 'loginImage'])->name('brand.login-image');

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     return view('landing');
// })->name('landing');

// Route::get('/', function () {
//     return redirect()->route('filament.admin.auth.login');
// });

// Home route with proper method handling
Route::get('/', [HomeController::class, 'index'])->name('home');

// SIMULASI
// Rute untuk preview HTML simulasi produk
Route::get('/simulasi/{record:slug}', [SimulasiDisplayController::class, 'show'])
    ->name('simulasi.show')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Rute untuk download PDF simulasi produk
Route::get('/simulasi/{record:slug}/download-pdf', [SimulasiDisplayController::class, 'downloadPdf'])
    ->name('simulasi.pdf')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Rute untuk draft kontrak simulasi produk
Route::get('/simulasi/{record:slug}/draft-kontrak', [SimulasiDisplayController::class, 'draftKontrak'])
    ->name('simulasi.draft-kontrak')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// USER REGISTRATION FORM PDF
// Rute untuk generate form pendaftaran karyawan kosong (PDF)
Route::get('/hr/user-form/blank', [UserFormPdfController::class, 'generateBlankForm'])
    ->name('user-form.blank')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Rute untuk generate form pendaftaran karyawan terisi (PDF)
Route::post('/hr/user-form/filled', [UserFormPdfController::class, 'generateFilledForm'])
    ->name('user-form.filled')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Rute untuk generate form terisi dari session (GET request)
Route::get('/hr/user-form/filled-session', [UserFormPdfController::class, 'generateFilledFormFromSession'])
    ->name('user-form.filled-session')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// PAYROLL SLIP GAJI
// Rute untuk download PDF slip gaji
Route::get('/payroll/{record}/slip-gaji', [App\Http\Controllers\PayrollSlipController::class, 'download'])
    ->name('payroll.slip-gaji.download')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// LEAVE APPROVAL DETAIL
// Rute untuk melihat detail persetujuan cuti
Route::get('/leave-request/{leaveRequest}/approval-detail', [App\Http\Controllers\LeaveApprovalController::class, 'show'])
    ->name('leave-request.approval-detail')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// DOCUMENT
Route::get('/document/{record}/stream', [DocumentController::class, 'stream'])
    ->name('document.stream')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// FRONTEND FEATURES
Route::get('/features/invoice', [FrontInvoiceController::class, 'index'])->name('front.invoice');
Route::get('/features/biaya', [BiayaFeatureController::class, 'index'])->name('front.biaya_feature');
Route::get('/features/laporan', [LaporanFeatureController::class, 'index'])->name('front.laporan_feature');
Route::get('/features/aset', [AsetFeatureController::class, 'index'])->name('front.aset_feature');
Route::get('/features/hris', [HrisFeatureController::class, 'index'])->name('front.hris_feature');
Route::get('/features/payroll', [PayrollFeatureController::class, 'index'])->name('front.payroll_feature');

// PRICING
Route::view('/harga', 'front.harga')->name('harga');

// REGISTRATION (PENDAFTARAN)
Route::view('/pendaftaran', 'front.pendaftaran')->name('pendaftaran');

// CONTACT
Route::view('/kontak', 'front.kontak')->name('kontak');

// BLOG
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/search', [BlogController::class, 'search'])->name('blog.search');
Route::get('/blog/category/{category}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.detail');

// INVOICE
Route::get('/invoice/{order}', [InvoiceOrderController::class, 'show'])
    ->name('invoice.show');
Route::get('/invoice/{order}/download', [InvoiceOrderController::class, 'download'])
    ->name('invoice.download');
Route::get('/invoice/{order}/print', [InvoiceOrderController::class, 'print'])
    ->name('invoice.print');
Route::post('/invoice/{order}/update-payment', [InvoiceOrderController::class, 'updatePayment'])
    ->name('invoice.update-payment')
    ->middleware(['web']);

// WIDGET ROUTE
// Widget yang langsung link ke processing
Route::get('/orders/reports/customer-payments/{status}', [ReportController::class, 'customerPayments'])
    ->name('reports.customer-payments');

// REPORT ROUTES
// Route untuk Laporan DataPembayaran HTML
Route::get('/laporan/pembayaran/html', [ReportController::class, 'generateDataPembayaranHtmlReport'])
    ->name('data-pembayaran.html-report')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

Route::get('/laporan/pembayaran/pdf', [ReportController::class, 'generateDataPembayaranPdfReport'])
    ->name('data-pembayaran.pdf-report')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Route untuk Laporan Pengeluaran Operasional HTML
Route::get('/laporan/expense-ops/html', [ReportController::class, 'generateExpenseOpsHtmlReport'])
    ->name('expense-ops.html-report')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// PRODUCT ROUTES
// Detail product
Route::get('/products/{product:slug}', [ProductDisplayController::class, 'show'])
    ->name('products.show')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

Route::get('/products/{product}/download-pdf', [ProductDisplayController::class, 'downloadPdf'])
    ->name('products.downloadPdf')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Route for product details (preview, download, print)
Route::get('/products/{product:slug}/details/{action}', [ProductDisplayController::class, 'details'])
    ->whereIn('action', ['preview', 'download', 'print'])
    ->name('products.details')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Route baru untuk ekspor detail produk ke Excel
Route::get('/products/{product}/export-excel-detail', [ProductDisplayController::class, 'exportDetailToExcel'])
    ->name('products.exportExcelDetail')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// EXPENSE ROUTES
// Route untuk Laporan Pengeluaran Wedding HTML
Route::get('/laporan/expense/html', [ReportController::class, 'generateExpenseHtmlReport'])
    ->name('expense.html-report')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Route untuk Laporan Pengeluaran Operasional PDF
Route::get('/laporan/expense-ops/pdf', [ReportController::class, 'generateExpenseOpsPdfReport'])
    ->name('expense-ops.pdf-report')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Route untuk Laporan Pengeluaran Wedding PDF
Route::get('/laporan/expense/pdf', [ReportController::class, 'generateExpensePdfReport'])
    ->name('expense.pdf-report')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Route untuk Laporan Net Cash Flow PDF Stream
Route::get('/laporan/net-cash-flow/pdf/stream', [ReportController::class, 'streamNetCashFlowPdf'])
    ->name('reports.net-cash-flow.pdf.stream')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// RUTE DATA PRIBADI
// Route untuk menampilkan form tambah data pribadi
Route::get('/data-pribadi/tambah', [FrontendDataPribadiController::class, 'create'])
    ->name('data-pribadi.create');

// Route untuk menampilkan daftar data pribadi
Route::get('/data-pribadi', [FrontendDataPribadiController::class, 'index'])
    ->name('data-pribadi.index');

// Route untuk menyimpan data baru dari form
Route::post('/data-pribadi', [FrontendDataPribadiController::class, 'store'])
    ->name('data-pribadi.store');

// AUTHENTICATION
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('front.login');
    Route::post('/login', [AuthController::class, 'login'])->name('front.login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('front.register');
    Route::post('/register', [AuthController::class, 'register'])->name('front.register.submit');
    
    // Google Login
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// PROFILE ROUTES
Route::middleware(\Filament\Http\Middleware\Authenticate::class)->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/dashboard', function () {
        return redirect()->route('filament.admin.pages.dashboard');
    })->name('dashboard');
    
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

// Route untuk Prospect (Original)
Route::get('/prospect', [ProspectController::class, 'create'])
    ->name('prospect.form');

Route::post('/prospect', [ProspectController::class, 'store'])
    ->name('prospect.store');

Route::get('/prospect/success', [ProspectController::class, 'success'])
    ->name('prospect.success');

Route::get('/debug-report', function() {
    $query = DataPembayaran::query()->with(['order', 'paymentMethod']);

    // Check raw count before join
    $rawCount = (clone $query)->count();

    // Check with join
    $joinedQuery = $query
        ->join('orders', 'data_pembayarans.order_id', '=', 'orders.id')
        ->select('data_pembayarans.*');
    
    $joinedCount = (clone $joinedQuery)->count();
    $joinedSql = $joinedQuery->toSql();
    $data = $joinedQuery->limit(5)->get();

    return [
        'raw_count' => $rawCount,
        'joined_count' => $joinedCount,
        'sql' => $joinedSql,
        'sample_data' => $data
    ];
});
