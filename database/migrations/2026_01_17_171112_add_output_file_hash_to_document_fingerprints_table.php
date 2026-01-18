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
            $table->string('output_file_hash', 64)->nullable()->after('fingerprint_hash')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_fingerprints', function (Blueprint $table) {
            $table->dropColumn('output_file_hash');
        });
    }
};
