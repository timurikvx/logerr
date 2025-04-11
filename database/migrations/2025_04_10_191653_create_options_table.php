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
        Schema::create('options', function (Blueprint $table) {
            //$table->id();
            $table->unsignedBigInteger('user');
            $table->unsignedBigInteger('team');
            $table->string('name');
            $table->string('guid')->index();
            $table->string('category')->nullable()->index();
            $table->json('data')->nullable();
            //$table->timestamps();
            $table->foreign('user')->references('id')->on('users');
            $table->primary(['user', 'team', 'name', 'category']);
            $table->index(['user', 'team', 'name', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
