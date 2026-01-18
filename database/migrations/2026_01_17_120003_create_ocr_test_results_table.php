<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocr_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watermark_job_id')->constrained()->onDelete('cascade');
            $table->foreignId('tested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ocr_engine', 32); // tesseract, google_vision, aws_textract
            $table->longText('extracted_text')->nullable();
            $table->boolean('watermark_detected')->default(false);
            $table->json('detected_patterns')->nullable(); // Patterns found in OCR
            $table->float('confidence_score')->nullable();
            $table->integer('processing_time_ms')->nullable();
            $table->integer('pages_processed')->nullable();
            $table->json('test_config')->nullable(); // OCR settings used
            $table->json('analysis_result')->nullable(); // Detailed analysis
            $table->timestamps();

            $table->index(['watermark_job_id', 'ocr_engine']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocr_test_results');
    }
};
