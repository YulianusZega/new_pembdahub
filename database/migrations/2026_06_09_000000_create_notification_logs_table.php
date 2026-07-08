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
        if (!Schema::hasTable('notification_logs')) {
            Schema::create('notification_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('notification_id')->nullable()->constrained('notifications')->onDelete('set null');
                $table->enum('channel', ['whatsapp', 'email', 'sms']);
                $table->string('recipient', 255);
                $table->text('message');
                $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
                $table->text('response')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->index('channel');
                $table->index('recipient');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
