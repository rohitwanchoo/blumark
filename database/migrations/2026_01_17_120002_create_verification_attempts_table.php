<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fingerprint_id')->nullable()->constrained('document_fingerprints')->nullOnDelete();
            $table->string('verification_token', 64)->nullable();
            $table->string('status', 32); // valid, invalid, expired, tampered, not_found
            $table->string('verification_method', 32); // token, upload, qr
            $table->string('request_ip', 45)->nullable();
            $table->json('request_data')->nullable(); // Additional request context
            $table->json('result_data')->nullable(); // Verification result details
            $table->timestamp('created_at');

            $table->index(['fingerprint_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('verification_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_attempts');
    }
};
