<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_fingerprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watermark_job_id')->constrained()->onDelete('cascade');
            $table->string('fingerprint_hash', 64); // SHA-256
            $table->foreignId('recipient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('unique_marker', 128)->unique(); // Encoded recipient identifier
            $table->text('metadata_json')->nullable(); // Encrypted document metadata
            $table->string('verification_token', 64)->unique();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();

            $table->index('fingerprint_hash');
            $table->index('verification_token');
            $table->index('recipient_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_fingerprints');
    }
};
