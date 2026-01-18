<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shared_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('watermark_job_id')->constrained()->onDelete('cascade');
            $table->string('token', 64)->unique(); // Secure random token
            $table->string('recipient_email')->nullable(); // Optional recipient tracking
            $table->string('recipient_name')->nullable();
            $table->timestamp('expires_at'); // Expiration timestamp
            $table->integer('download_count')->default(0);
            $table->integer('max_downloads')->nullable(); // Optional download limit
            $table->string('password_hash')->nullable(); // Optional password protection
            $table->timestamp('last_accessed_at')->nullable();
            $table->string('last_accessed_ip')->nullable();
            $table->timestamps();

            $table->index(['token', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_links');
    }
};
