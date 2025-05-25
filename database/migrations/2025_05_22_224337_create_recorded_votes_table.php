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
        Schema::create('recorded_votes', function (Blueprint $table) {
            $table->id(); 

            $table->foreignId('election_id')
                  ->constrained('elections')
                  ->onDelete('cascade'); 

            $table->foreignId('candidate_id')
                  ->nullable()
                  ->constrained('candidates')
                  ->onDelete('cascade'); 

            $table->boolean('is_blank_vote')->default(false); 

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recorded_votes');
    }
};