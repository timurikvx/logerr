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
        Schema::create('notifications_fields', function (Blueprint $table) {
            $table->unsignedBigInteger('option');
            $table->string('field');
            $table->string('value');
            $table->foreign('option')->references('id')->on('notifications_options');
            $table->primary(['option', 'field']);
            $table->index(['option', 'field']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications_fields');
    }
};
