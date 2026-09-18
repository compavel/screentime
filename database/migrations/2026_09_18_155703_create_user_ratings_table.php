<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('tmdb_id');
            $table->string('title');
            $table->string('poster_path')->nullable();
            $table->string('media_type')->default('movie');
            $table->integer('rating'); // 1-5 stars
            $table->string('genre_ids')->nullable(); // comma separated: "28,12,878"
            $table->timestamps();

            $table->unique(['user_id', 'tmdb_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_ratings');
    }
};
