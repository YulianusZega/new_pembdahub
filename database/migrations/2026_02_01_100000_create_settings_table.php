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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->string('group')->default('general'); // general, payment, notification, etc
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default late fee settings
        DB::table('settings')->insert([
            [
                'key' => 'late_fee_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'description' => 'Enable automatic late payment fee calculation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_fee_grace_period',
                'value' => '3',
                'type' => 'integer',
                'group' => 'payment',
                'description' => 'Days after due date before late fee applies',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_fee_amount',
                'value' => '10000',
                'type' => 'integer',
                'group' => 'payment',
                'description' => 'Late payment fee amount (Rp)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_fee_type',
                'value' => 'fixed',
                'type' => 'string',
                'group' => 'payment',
                'description' => 'Late fee type: fixed or percentage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
