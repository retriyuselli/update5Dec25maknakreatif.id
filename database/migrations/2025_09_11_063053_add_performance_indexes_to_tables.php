<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $dbName = DB::getDatabaseName();

        // Add compound index for nota_dinas_details
        $notaIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'nota_dinas_details')
            ->where('index_name', 'idx_nota_dinas_details_compound')
            ->exists();
        if (Schema::hasTable('nota_dinas_details') && ! $notaIndexExists) {
            Schema::table('nota_dinas_details', function (Blueprint $table) {
                $table->index(['nota_dinas_id', 'jenis_pengeluaran', 'vendor_id'], 'idx_nota_dinas_details_compound');
            });
        }

        // Add compound index for expenses
        $expensesIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'expenses')
            ->where('index_name', 'idx_expenses_order_detail')
            ->exists();
        if (Schema::hasTable('expenses') && ! $expensesIndexExists) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->index(['order_id', 'nota_dinas_detail_id'], 'idx_expenses_order_detail');
            });
        }

        // Add compound index for orders
        $ordersIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'orders')
            ->where('index_name', 'idx_orders_status_dates')
            ->exists();
        if (Schema::hasTable('orders') && ! $ordersIndexExists) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index(['status', 'closing_date', 'created_at'], 'idx_orders_status_dates');
            });
        }

        // Add index for data_pembayarans (corrected table name)
        $dataPembayaranIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'data_pembayarans')
            ->where('index_name', 'idx_data_pembayaran_order')
            ->exists();
        if (Schema::hasTable('data_pembayarans') && ! $dataPembayaranIndexExists) {
            Schema::table('data_pembayarans', function (Blueprint $table) {
                $table->index(['order_id', 'tgl_bayar'], 'idx_data_pembayaran_order');
            });
        }

        // Add index for products price fields
        $productsIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'products')
            ->where('index_name', 'idx_products_price_fields')
            ->exists();
        if (Schema::hasTable('products') && ! $productsIndexExists) {
            Schema::table('products', function (Blueprint $table) {
                $table->index(['product_price', 'pengurangan'], 'idx_products_price_fields');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $dbName = DB::getDatabaseName();
        $notaIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'nota_dinas_details')
            ->where('index_name', 'idx_nota_dinas_details_compound')
            ->exists();
        if (Schema::hasTable('nota_dinas_details') && $notaIndexExists) {
            Schema::table('nota_dinas_details', function (Blueprint $table) {
                $table->dropIndex('idx_nota_dinas_details_compound');
            });
        }

        $expensesIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'expenses')
            ->where('index_name', 'idx_expenses_order_detail')
            ->exists();
        if (Schema::hasTable('expenses') && $expensesIndexExists) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropIndex('idx_expenses_order_detail');
            });
        }

        $ordersIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'orders')
            ->where('index_name', 'idx_orders_status_dates')
            ->exists();
        if (Schema::hasTable('orders') && $ordersIndexExists) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex('idx_orders_status_dates');
            });
        }

        $dataPembayaranIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'data_pembayarans')
            ->where('index_name', 'idx_data_pembayaran_order')
            ->exists();
        if (Schema::hasTable('data_pembayarans') && $dataPembayaranIndexExists) {
            Schema::table('data_pembayarans', function (Blueprint $table) {
                $table->dropIndex('idx_data_pembayaran_order');
            });
        }

        $productsIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'products')
            ->where('index_name', 'idx_products_price_fields')
            ->exists();
        if (Schema::hasTable('products') && $productsIndexExists) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex('idx_products_price_fields');
            });
        }
    }
};
