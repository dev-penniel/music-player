<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTracksTable extends Migration
{
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {

            $table->id();

            // Main Artist
            $table->foreignId('artist_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('album_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Core Track Info
            $table->string('title');
            $table->string('slug')->unique();
            $table->integer('track_number')->nullable();
            $table->integer('duration')->nullable(); // seconds

            // Files
            $table->string('file_path');
            $table->string('cover_path')->nullable();

            // Publishing
            $table->date('release_date')->nullable();
            $table->boolean('is_published')->default(false);

            // Streaming stats
            $table->unsignedBigInteger('plays')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
