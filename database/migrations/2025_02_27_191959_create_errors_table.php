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
        Schema::create('errors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->dateTime('date')->index();
            $table->string('category')->nullable()->index();
            $table->string('sub_category')->nullable()->index();
            $table->string('sender_guid')->nullable()->index();
            $table->string('sender_name')->nullable()->index();
            $table->longText('text');
            $table->string('type');
            $table->unsignedBigInteger('code')->nullable()->index();
            $table->string('user')->nullable()->index();
            $table->string('device')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('region')->nullable()->index();
            $table->string('version')->nullable()->index();
            $table->longText('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('errors');
    }
};
