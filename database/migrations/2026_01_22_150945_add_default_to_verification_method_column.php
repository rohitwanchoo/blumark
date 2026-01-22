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
        Schema::table('verification_attempts', function (Blueprint $table) {
            // Make verification_method nullable with a default value to prevent insert errors
            $table->string('verification_method', 32)->default('token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_attempts', function (Blueprint $table) {
            // Revert to original non-nullable without default
            $table->string('verification_method', 32)->default(null)->change();
        });
    }
};
