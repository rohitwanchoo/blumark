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
        Schema::table('smtp_settings', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('name'); // gmail, sendgrid, mailgun, ses, custom
            $table->string('provider_type')->default('custom')->after('provider'); // oauth, api_key, custom
            $table->text('provider_data')->nullable()->after('provider_type'); // Encrypted JSON for API keys, etc.
            $table->text('oauth_tokens')->nullable()->after('provider_data'); // Encrypted OAuth tokens
            $table->timestamp('token_expires_at')->nullable()->after('oauth_tokens');

            // Make some fields nullable for provider-based configs
            $table->string('host')->nullable()->change();
            $table->string('username')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smtp_settings', function (Blueprint $table) {
            $table->dropColumn([
                'provider',
                'provider_type',
                'provider_data',
                'oauth_tokens',
                'token_expires_at',
            ]);
        });
    }
};
