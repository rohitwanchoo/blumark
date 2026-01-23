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
            $table->foreignId('smtp_setting_id')->nullable()->after('email_template_id')->constrained('smtp_settings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lender_distributions', function (Blueprint $table) {
            $table->dropForeign(['smtp_setting_id']);
            $table->dropColumn('smtp_setting_id');
        });
    }
};
