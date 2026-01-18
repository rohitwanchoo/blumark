<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watermark_job_id')->constrained()->onDelete('cascade');
            $table->foreignId('fingerprint_id')->nullable()->constrained('document_fingerprints')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 32); // download, view, verify, share, preview
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('geo_country', 2)->nullable();
            $table->string('geo_city')->nullable();
            $table->json('metadata')->nullable(); // Additional context
            $table->timestamp('created_at');

            $table->index(['watermark_job_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_access_logs');
    }
};
