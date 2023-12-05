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
        Schema::create('statistics', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->integer('victories', false, true)->default(0);
            $table->integer('defeats', false, true)->default(0);
            $table->integer('draws', false, true)->default(0);
            $table->integer('technical_victories', false, true)->default(0);
            $table->integer('technical_defeats', false, true)->default(0);
            $table->integer('user_id', false, true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
