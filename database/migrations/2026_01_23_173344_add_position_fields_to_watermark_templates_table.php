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
        Schema::table('watermark_templates', function (Blueprint $table) {
            // Position: diagonal, top-left, top-right, top-center, bottom-left, bottom-right, bottom-center, center
            $table->string('position')->default('diagonal')->after('opacity');
            // Rotation angle: 0, 45, 90, 270, etc.
            $table->integer('rotation')->default(45)->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('watermark_templates', function (Blueprint $table) {
            $table->dropColumn(['position', 'rotation']);
        });
    }
};
