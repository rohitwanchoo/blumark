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
        Schema::create('lender_distribution_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lender_distribution_id')->constrained()->onDelete('cascade');
            $table->foreignId('lender_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('watermark_job_id')->nullable()->constrained()->onDelete('set null');
            $table->json('lender_snapshot');
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->string('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->enum('sent_via', ['email_attachment', 'email_link'])->nullable();
            $table->timestamps();

            $table->index(['lender_distribution_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lender_distribution_items');
    }
};
