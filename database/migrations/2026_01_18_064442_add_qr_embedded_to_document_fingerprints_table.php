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
        Schema::table('document_fingerprints', function (Blueprint $table) {
            $table->boolean('qr_embedded')->default(false)->after('verification_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_fingerprints', function (Blueprint $table) {
            $table->dropColumn('qr_embedded');
        });
    }
};
