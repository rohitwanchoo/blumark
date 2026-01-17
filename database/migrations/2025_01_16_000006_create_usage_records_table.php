<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('watermark_job_id')->nullable()->constrained()->onDelete('set null');
            $table->date('usage_date');
            $table->integer('jobs_count')->default(1);
            $table->integer('pages_count')->default(0);
            $table->string('source');
            $table->timestamps();

            $table->index(['user_id', 'usage_date']);
            $table->index('usage_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_records');
    }
};
