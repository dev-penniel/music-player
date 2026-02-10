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
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('album')->nullable();
            $table->string('file_path'); // storage path e.g. 'tracks/song.mp3'
            $table->string('cover_path')->nullable(); // storage path e.g. 'covers/cover.jpg'
            $table->integer('duration')->nullable(); // seconds
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
