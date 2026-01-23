<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watermark_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Template name for easy identification
            $table->string('iso', 100); // ISO name
            $table->string('lender', 100); // Lender name
            $table->string('lender_email')->nullable(); // Optional email for quick sending
            $table->integer('font_size')->default(15);
            $table->string('color', 7)->default('#878787');
            $table->integer('opacity')->default(10);
            $table->boolean('is_default')->default(false);
            $table->integer('usage_count')->default(0); // Track usage for sorting
            $table->timestamps();

            $table->index(['user_id', 'usage_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watermark_templates');
    }
};
