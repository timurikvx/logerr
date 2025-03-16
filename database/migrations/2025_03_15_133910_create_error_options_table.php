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
        Schema::create('error_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user')->index();
            $table->unsignedBigInteger('team')->index();
            $table->string('name');
            $table->string('guid');
            $table->json('data')->nullable();
            $table->timestamps();
            $table->foreign('user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_options');
    }
};
