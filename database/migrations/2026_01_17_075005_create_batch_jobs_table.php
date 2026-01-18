<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable(); // Optional batch name
            $table->string('iso', 100);
            $table->string('lender', 100);
            $table->json('settings'); // Watermark settings (font_size, color, opacity)
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->integer('total_files')->default(0);
            $table->integer('processed_files')->default(0);
            $table->integer('failed_files')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Add batch_job_id to watermark_jobs table
        Schema::table('watermark_jobs', function (Blueprint $table) {
            $table->foreignId('batch_job_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('watermark_jobs', function (Blueprint $table) {
            $table->dropForeign(['batch_job_id']);
            $table->dropColumn('batch_job_id');
        });

        Schema::dropIfExists('batch_jobs');
    }
};
