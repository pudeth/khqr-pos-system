<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 191)->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, text, image, boolean
            $table->string('group')->default('general'); // general, branding, payment, etc.
            $table->timestamps();
        });

        // Insert default settings
        DB::table('store_settings')->insert([
            [
                'key' => 'store_name',
                'value' => 'POSPAY',
                'type' => 'string',
                'group' => 'branding',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'store_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'branding',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'store_tagline',
                'value' => 'KHQR PAYMENT SYSTEM',
                'type' => 'string',
                'group' => 'branding',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'store_address',
                'value' => '',
                'type' => 'text',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'store_phone',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('store_settings');
    }
};