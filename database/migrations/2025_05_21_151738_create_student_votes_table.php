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
        Schema::create('student_votes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                  ->constrained('students') 
                  ->onDelete('cascade'); 

            $table->foreignId('election_id')
                  ->constrained('elections') 
                  ->onDelete('cascade'); 

            $table->timestamp('voted_at')->useCurrent(); 

            $table->timestamps();

            $table->unique(['student_id', 'election_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_votes');
    }
};
