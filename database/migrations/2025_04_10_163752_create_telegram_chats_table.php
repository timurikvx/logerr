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
        Schema::create('telegram_chats', function (Blueprint $table) {
            $table->id();
            $table->string('guid');
            $table->unsignedBigInteger('team')->index();
            $table->string('name');
            $table->string('token');
            $table->string('chat_id');
            $table->unsignedBigInteger('creator');
            $table->timestamps();
            $table->index('guid');
            $table->index(['team', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_chats');
    }
};
