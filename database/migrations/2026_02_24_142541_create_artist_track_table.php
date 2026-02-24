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
        Schema::create('artist_track', function (Blueprint $table) {
            $table->id();

            $table->foreignId('track_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('artist_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Role column (MAIN / FEATURED / PRODUCER etc)
            $table->string('role')->default('featured');

            $table->timestamps();

            // Prevent duplicate artist for same track
            $table->unique(['track_id', 'artist_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_track');
    }
};
