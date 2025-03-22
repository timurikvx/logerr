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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user')->index();
            $table->string('type')->index();
            $table->string('guid');
            $table->string('title');
            $table->text('content');
            $table->json('data')->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('from')->index();
            $table->boolean('completed')->default(false);
            $table->boolean('missed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
