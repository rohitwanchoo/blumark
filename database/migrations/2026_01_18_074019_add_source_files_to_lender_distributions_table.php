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
        Schema::table('lender_distributions', function (Blueprint $table) {
            // Add JSON column for multiple source files
            $table->json('source_files')->nullable()->after('source_path');
        });

        Schema::table('lender_distribution_items', function (Blueprint $table) {
            // Add index to track which source file this item belongs to
            $table->unsignedInteger('source_file_index')->default(0)->after('lender_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lender_distribution_items', function (Blueprint $table) {
            $table->dropColumn('source_file_index');
        });

        Schema::table('lender_distributions', function (Blueprint $table) {
            $table->dropColumn('source_files');
        });
    }
};
