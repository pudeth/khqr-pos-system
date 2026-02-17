<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, update all existing sales to KHQR
        DB::table('sales')->update(['payment_method' => 'KHQR']);
        
        // Then modify the column to only allow KHQR
        DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('KHQR') DEFAULT 'KHQR'");
    }

    public function down(): void
    {
        // Restore original payment methods
        DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('CASH', 'KHQR', 'CARD') DEFAULT 'CASH'");
    }
};
