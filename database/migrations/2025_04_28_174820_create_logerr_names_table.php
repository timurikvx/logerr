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
        Schema::create('logerr_names', function (Blueprint $table) {
            $table->string('type');
            $table->string('field');
            $table->string('value');
            $table->primary(['type', 'field', 'value']);
            $table->index(['type', 'field', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logerr_names');
    }
};
