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
        // ================================================================
        // 6. MESSAGING, NOTIFICATIONS & LOGS
        // ================================================================

        // Tabel Pesan (Messaging System)
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->string('attachment_path', 255)->nullable()->comment('Path file attachment (max 2MB)');
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();
            $table->timestamps();

            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('is_read');
        });

        // Tabel Notifikasi
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type', 50)->comment('bill_reminder, alpha_warning, payment_success, grade_posted, etc');
            $table->string('title', 200);
            $table->text('message');
            $table->string('related_model', 100)->nullable()->comment('Model yang terkait (Student, Bill, dll)');
            $table->bigInteger('related_id')->nullable()->comment('ID dari related model');
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
            $table->index('is_read');
        });

        // Tabel Activity Log (untuk audit trail)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
            $table->string('action', 100)->comment('create, update, delete, login, etc');
            $table->string('description', 255)->nullable();
            $table->string('model_type', 100)->nullable()->comment('User, Student, Grade, Payment, etc');
            $table->bigInteger('model_id')->nullable();
            $table->text('changes')->nullable()->comment('JSON: old values & new values');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('logged_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('school_id');
            $table->index('model_type');
            $table->index('created_at');
        });

        // Tabel Email Queue (untuk background email sending)
        Schema::create('email_queue', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_email', 100);
            $table->string('subject', 200);
            $table->text('body');
            $table->string('mail_type', 50)->comment('welcome, password_reset, bill_reminder, receipt, etc');
            $table->boolean('is_sent')->default(false);
            $table->dateTime('sent_at')->nullable();
            $table->integer('retry_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('is_sent');
            $table->index('mail_type');
        });

        // Tabel Device Tokens (untuk PWA push notifications)
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('device_token', 500)->unique()->comment('Push notification token dari PWA');
            $table->string('device_name', 100)->nullable()->comment('Device name / browser name');
            $table->string('device_type', 50)->nullable()->comment('mobile, tablet, desktop');
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });

        // Tabel Login History
        Schema::create('login_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->string('user_agent', 255)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            $table->dateTime('logged_in_at');
            $table->dateTime('logged_out_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('logged_in_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_history');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('email_queue');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('messages');
    }
};
