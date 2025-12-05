<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Tambah kolom nota_dinas_detail_id sebagai foreign key ke nota_dinas_details
            $table->foreignId('nota_dinas_detail_id')->nullable()->after('no_nd')->constrained('nota_dinas_details')->onDelete('set null');

            // Juga tambahkan kolom lain yang ada di fillable tapi belum di database
            $table->foreignId('nota_dinas_id')->nullable()->after('nota_dinas_detail_id')->constrained('nota_dinas')->onDelete('set null');
            $table->string('payment_stage')->nullable()->after('nota_dinas_id');
            $table->string('account_holder')->nullable()->after('payment_stage');
            $table->string('bank_name')->nullable()->after('account_holder');
            $table->string('bank_account')->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['nota_dinas_detail_id']);
            $table->dropColumn('nota_dinas_detail_id');

            $table->dropForeign(['nota_dinas_id']);
            $table->dropColumn('nota_dinas_id');

            $table->dropColumn(['payment_stage', 'account_holder', 'bank_name', 'bank_account']);
        });
    }
};
