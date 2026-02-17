<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 50)->unique();
            $table->string('name', 191)->nullable();
            $table->string('address', 255)->nullable(); // Street/House number
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->integer('total_points')->default(0);
            $table->integer('available_points')->default(0);
            $table->timestamps();
            
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};