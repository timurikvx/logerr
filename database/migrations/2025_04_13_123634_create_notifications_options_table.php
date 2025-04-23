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
        Schema::create('notifications_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team');
            $table->string('guid');
            $table->string('name');
            $table->string('type');
            $table->unsignedBigInteger('chat');
            $table->unsignedInteger('minutes')->default(0);
            $table->unsignedInteger('count')->default(0);
            $table->unsignedInteger('every')->default(30);
            $table->json('option')->nullable();
            $table->timestamps();
            $table->foreign('chat')->references('id')->on('telegram_chats');
            $table->index(['team', 'type']);
            $table->index(['team', 'guid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications_options');
    }
};
