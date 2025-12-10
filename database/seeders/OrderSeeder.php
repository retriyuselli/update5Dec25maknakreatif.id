<?php

namespace Database\Seeders;

use App\Models\DataPembayaran;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Starting OrderSeeder...');

        // Optional: Clean previous seeder data
        $this->cleanPreviousSeederData();

        // Get required data
        $prospects = Prospect::all();
        $users = User::all();
        $employees = Employee::all();
        $products = Product::all();
        $vendors = Vendor::all();

        if ($prospects->isEmpty()) {
            $this->command->error('No prospects found. Please run ProspectSeeder first.');

            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run DatabaseSeeder first.');

            return;
        }

        if ($products->isEmpty()) {
            $this->command->error('No products found. Please run ProductSeeder first.');

            return;
        }

        // Create 50 orders with only basic project information (Wizard Step 1: Informasi Proyek)
        $orders = [];
        $statuses = ['pending', 'processing', 'done', 'cancelled'];

        // Get the highest existing order number to avoid duplicates
        $lastOrderNumber = Order::whereRaw('number LIKE "MW-%"')
            ->orderBy('number', 'desc')
            ->value('number');

        $startNumber = 100000;
        if ($lastOrderNumber) {
            preg_match('/MW-(\d+)/', $lastOrderNumber, $matches);
            $startNumber = isset($matches[1]) ? (int) $matches[1] + 1 : 100000;
        }

        for ($i = 1; $i <= 50; $i++) {
            $prospect = $prospects->random();
            $user = $users->random();
            $employee = $employees->isEmpty() ? null : $employees->random();
            $status = collect($statuses)->random();

            // Generate unique order number (following the format from OrderResource: MW-XXXXXX)
            $orderNumber = 'MW-'.($startNumber + $i - 1);

            // Generate dates (all in 2025)
            $createdDate = Carbon::create(2025, rand(1, 7), rand(1, 28))->addHours(rand(8, 18))->addMinutes(rand(0, 59));

            // Basic project information only (Step 1: Informasi Proyek)
            $orders[] = [
                'number' => $orderNumber,
                'prospect_id' => $prospect->id,
                'name' => $prospect->name_event,
                'slug' => Str::slug($prospect->name_event.'-'.$orderNumber),
                'user_id' => $user->id,
                'employee_id' => $employee?->id,
                'no_kontrak' => 'KONTR-'.date('Y').'-'.str_pad($i, 4, '0', STR_PAD_LEFT),
                'pax' => (int) rand(100, 500),
                'status' => $status,
                'total_price' => 0, // Required field, set to 0 for automatic calculation
                'doc_kontrak' => 'doc_kontrak/contoh/Contoh Pdf Kontrak.pdf', // Use existing contract PDF
                'note' => '<p>Wedding order untuk '.$prospect->name_event.' dengan status '.$status.'.</p><p><strong>Informasi Dasar:</strong></p><ul><li>Jumlah tamu: '.rand(100, 500).' pax</li><li>Status: '.ucfirst($status).'</li><li>Manajer Akun: '.$user->name.'</li>'.($employee ? '<li>Manajer Acara: '.$employee->name.'</li>' : '').'</ul>',
                'created_at' => $createdDate,
                'updated_at' => $createdDate,
            ];
        }

        // Create orders with basic project information, products, and payment data
        foreach ($orders as $orderData) {
            $order = Order::create($orderData);

            // Create order items (products) for the order ensuring total_price ≥ 150,000,000
            $totalProductPrice = 0;
            $itemsAdded = 0;
            while ($totalProductPrice < 150_000_000 || $itemsAdded < 2) {
                $product = $products->random();
                $quantity = rand(1, 3); // 1-3 quantity per product
                $unitPrice = rand(10_000_000, 75_000_000); // 10M - 75M per product to reach threshold
                $itemTotal = $quantity * $unitPrice;
                $totalProductPrice += $itemTotal;
                $itemsAdded++;

                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);
            }

            // Update order's total_price based on products
            $order->update([
                'total_price' => $totalProductPrice,
            ]);

            // Create payment data (Data Pembayaran) for non-cancelled orders
            if ($order->status !== 'cancelled') {
                $paymentCount = rand(1, 3); // 1-3 payments per order
                $totalPaymentAmount = rand(5000000, 50000000); // 5M - 50M total payments
                $remainingAmount = $totalPaymentAmount;
                $firstPaymentDate = null; // Track first payment date for closing date

                for ($j = 0; $j < $paymentCount; $j++) {
                    // Calculate payment amount
                    $paymentAmount = ($j === $paymentCount - 1)
                        ? $remainingAmount
                        : rand(1000000, min($remainingAmount - 1000000, 20000000));

                    $remainingAmount -= $paymentAmount;

                    // Generate payment date (within 2025, after order creation)
                    $paymentDate = $order->created_at->copy()->addDays(rand(1, 60));

                    // Ensure payment date stays within 2025
                    if ($paymentDate->year > 2025) {
                        $paymentDate = Carbon::create(2025, 12, rand(1, 31))->addHours(rand(8, 18))->addMinutes(rand(0, 59));
                    }

                    // Track the earliest payment date for closing date
                    if ($firstPaymentDate === null || $paymentDate->lt($firstPaymentDate)) {
                        $firstPaymentDate = $paymentDate;
                    }

                    // Create payment record
                    DataPembayaran::create([
                        'order_id' => $order->id,
                        'nominal' => $paymentAmount,
                        'tgl_bayar' => $paymentDate,
                        'keterangan' => 'Pembayaran '.($j + 1).' untuk '.$order->name,
                        'payment_method_id' => PaymentMethod::inRandomOrder()->first()?->id ?? 1,
                        'kategori_transaksi' => 'uang_masuk',
                        'image' => 'doc_kontrak/contoh/Contoh Bukti Rekening.jpg', // Use existing payment proof image
                        'created_at' => $order->created_at,
                        'updated_at' => $order->created_at,
                    ]);
                }

                // Update order's paid_amount and closing_date based on payments
                $order->update([
                    'paid_amount' => $totalPaymentAmount,
                    'closing_date' => $firstPaymentDate, // Set closing date from first payment
                ]);
            }

            // Create expenses (Pengeluaran) for non-cancelled orders
            if ($order->status !== 'cancelled' && $vendors->isNotEmpty()) {
                $expenseCount = rand(2, 5); // 2-5 expenses per order
                $totalExpenseAmount = 0;

                for ($m = 0; $m < $expenseCount; $m++) {
                    $vendor = $vendors->random();
                    $expenseAmount = rand(500000, 10000000); // 500K - 10M per expense
                    $totalExpenseAmount += $expenseAmount;

                    // Generate expense date (within 2025, after order creation)
                    $expenseDate = $order->created_at->copy()->addDays(rand(5, 90));

                    // Ensure expense date stays within 2025
                    if ($expenseDate->year > 2025) {
                        $expenseDate = Carbon::create(2025, 12, rand(1, 31))->addHours(rand(8, 18))->addMinutes(rand(0, 59));
                    }

                    // Create expense record
                    Expense::create([
                        'order_id' => $order->id,
                        'vendor_id' => $vendor->id,
                        'amount' => $expenseAmount,
                        'note' => 'Pembayaran ke '.$vendor->name.' untuk '.$order->name,
                        'date_expense' => $expenseDate,
                        'payment_method_id' => PaymentMethod::inRandomOrder()->first()?->id ?? 1,
                        'kategori_transaksi' => 'uang_keluar',
                        'no_nd' => rand(1000, 9999), // Random nota dinas number (format: ND-0XXXX di form)
                        'image' => 'doc_kontrak/contoh/Contoh Invoice.jpg', // Use existing invoice image
                        'created_at' => $order->created_at,
                        'updated_at' => $order->created_at,
                    ]);
                }
            }
        }

        $this->command->info('50 orders with project information, products (≥150jt), payments, and expenses created successfully!');
    }

    /**
     * Clean previous seeder data to avoid duplicates
     */
    private function cleanPreviousSeederData(): void
    {
        // Find orders created by seeder (with MW-XXXXXX pattern) including soft deleted
        $seederOrders = Order::withTrashed()->whereRaw('number LIKE "MW-%"')->get();

        if ($seederOrders->count() > 0) {
            $this->command->warn("Found {$seederOrders->count()} previous seeder orders (including soft deleted).");

            if ($this->command->confirm('Do you want to permanently delete previous seeder data before creating new ones?', true)) {
                $this->command->info('Permanently cleaning previous OrderSeeder data...');

                foreach ($seederOrders as $order) {
                    // Delete related data first (to maintain referential integrity)
                    // For DataPembayaran
                    \App\Models\DataPembayaran::withTrashed()->where('order_id', $order->id)->forceDelete();

                    // For Expenses
                    \App\Models\Expense::withTrashed()->where('order_id', $order->id)->forceDelete();

                    // For OrderProduct (doesn't use soft delete)
                    \App\Models\OrderProduct::where('order_id', $order->id)->delete();

                    // Force delete the order (permanent deletion)
                    $order->forceDelete();
                }

                $this->command->info('Previous seeder data permanently cleaned!');
            } else {
                $this->command->info('Keeping existing data. New orders will have incremented numbers.');
            }
        } else {
            $this->command->info('No previous seeder data found.');
        }
    }
}
