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
        Schema::create('notification_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('team');
            $table->unsignedBigInteger('option')->index();
            $table->dateTime('date');
            $table->text('fields');
            $table->longText('message');
            $table->longText('data')->nullable();
            $table->boolean('sended')->default(false);

            $table->primary(['team', 'option', 'date']);
            $table->index(['team', 'option', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_messages');
    }
};
