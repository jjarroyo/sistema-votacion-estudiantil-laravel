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
        Schema::table('elections', function (Blueprint $table) {
            $table->string('voting_session_token')->nullable()->unique()->after('status');
            $table->timestamp('token_generated_at')->nullable()->after('voting_session_token');
            // Opcional: $table->timestamp('token_expires_at')->nullable()->after('token_generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropColumn(['voting_session_token', 'token_generated_at']);
        });
    }
};
