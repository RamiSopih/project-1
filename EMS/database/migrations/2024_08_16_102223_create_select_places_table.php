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
        Schema::create('select_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('price');
            $table->string('name');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('select_places');
    }
};
