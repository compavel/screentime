<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('tmdb_id');
            $table->string('title');
            $table->string('poster_path')->nullable();
            $table->enum('media_type', ['movie', 'tv']);
            $table->enum('status', ['want_to_watch', 'watching', 'watched'])->default('want_to_watch');
            $table->integer('rating')->nullable();
            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tmdb_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};
