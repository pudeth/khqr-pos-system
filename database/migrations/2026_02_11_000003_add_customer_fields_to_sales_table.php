<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('payment_id')->constrained()->onDelete('set null');
            $table->string('customer_address', 255)->nullable()->after('customer_phone');
            $table->integer('points_earned')->default(0)->after('customer_address');
            $table->integer('points_used')->default(0)->after('points_earned');
            $table->decimal('points_discount', 10, 2)->default(0)->after('points_used');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'customer_address', 'points_earned', 'points_used', 'points_discount']);
        });
    }
};