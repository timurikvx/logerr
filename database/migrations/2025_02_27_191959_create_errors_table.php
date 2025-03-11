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
            $this->table($table);
        });

        Schema::create('logs', function (Blueprint $table) {
            $this->table($table);
        });

    }

    public function table(Blueprint &$table)
    {
        $table->id();
        $table->string('name')->index();
        $table->unsignedBigInteger('team')->index();
        $table->dateTime('date')->index();
        $table->string('guid')->nullable()->index();
        $table->string('category')->nullable()->index();
        $table->string('sub_category')->nullable()->index();
        $table->string('sender_guid')->nullable()->index();
        $table->string('sender_name')->nullable()->index();
        $table->string('type')->index();
        $table->unsignedBigInteger('code')->nullable()->index();
        $table->string('user')->nullable()->index();
        $table->string('device')->nullable()->index();
        $table->string('city')->nullable()->index();
        $table->string('region')->nullable()->index();
        $table->string('version')->nullable()->index();
        $table->unsignedInteger('duration')->nullable()->index();
        $table->longText('data')->nullable();
        $table->unsignedBigInteger('len')->default(0);
        $table->index(['name', 'team', 'date']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('errors');
    }
};
