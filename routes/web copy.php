<?php

use App\Http\Controllers\FrontendDataPribadiController;
use App\Http\Controllers\InvoiceOrderController;
use App\Http\Controllers\OrderProfitLossController;
use App\Http\Controllers\ProductDisplayController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SimulasiDisplayController;
use Illuminate\Support\Facades\Route;

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
Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('home');

// SIMULASI
// Rute untuk preview HTML simulasi produk
Route::get('/simulasi/{record:slug}', [SimulasiDisplayController::class, 'show'])
    ->name('simulasi.show')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Rute untuk download PDF simulasi produk
Route::get('/simulasi/{record:slug}/download-pdf', [SimulasiDisplayController::class, 'downloadPdf'])
    ->name('simulasi.pdf')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

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

// LABA RUGI ORDER
// Route untuk preview laporan laba rugi per order
Route::get('/orders/{order}/profit-loss-preview', [OrderProfitLossController::class, 'preview'])
    ->name('orders.profit_loss.preview')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// Route untuk download PDF laporan laba rugi per order
Route::get('/orders/{order}/profit-loss-download', [OrderProfitLossController::class, 'download'])
    ->name('orders.profit_loss.download')
    ->middleware(\Filament\Http\Middleware\Authenticate::class);

// WIDGET ROUTE
// Widget yang langsung link ke processing
Route::get('/orders/reports/customer-payments/{status}', [ReportController::class, 'customerPayments'])
    ->name('reports.customer-payments');

// REPORT ROUTES
// Route untuk Laporan DataPembayaran HTML
Route::get('/laporan/pembayaran/html', [ReportController::class, 'generateDataPembayaranHtmlReport'])
    ->name('data-pembayaran.html-report')
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

// Route untuk Prospect (Original)
Route::get('/prospect', [ProspectController::class, 'create'])
    ->name('prospect.form');

Route::post('/prospect', [ProspectController::class, 'store'])
    ->name('prospect.store');

Route::get('/prospect/success', [ProspectController::class, 'success'])
    ->name('prospect.success');

Route::post('/prospect/check-email', [ProspectController::class, 'checkEmail'])
    ->name('prospect.check-email');

// Route untuk ProspectApp (New - Modern Form)
Route::get('/prospect-app', [App\Http\Controllers\ProspectAppController::class, 'create'])
    ->name('prospect-app.form');

Route::post('/prospect-app', [App\Http\Controllers\ProspectAppController::class, 'store'])
    ->name('prospect-app.store');

Route::get('/prospect-app/success', [App\Http\Controllers\ProspectAppController::class, 'success'])
    ->name('prospect-app.success');

Route::post('/prospect-app/check-email', [App\Http\Controllers\ProspectAppController::class, 'checkEmail'])
    ->name('prospect-app.check-email');

// Route untuk download PDF Proposal ProspectApp
Route::get('/prospect-app/{prospectApp}/proposal/pdf', [App\Http\Controllers\ProspectAppController::class, 'generateProposalPdf'])
    ->middleware('auth')
    ->name('prospect-app.proposal.pdf');

// Route untuk download PDF Laporan Keuangan
Route::get('/laporan-keuangan/download-pdf', [App\Filament\Pages\LaporanKeuangan::class, 'handleDownloadPdf'])
    ->middleware('auth')
    ->name('laporan-keuangan.download-pdf');

// FALLBACK ROUTE
Route::fallback(function () {
    return redirect()->route('filament.admin.auth.login');
});
