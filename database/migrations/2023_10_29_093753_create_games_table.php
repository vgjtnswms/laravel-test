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
        Schema::create('games', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->integer('host_user_id', false, true)->index('index_host_user_id');
            $table->integer('opponent_user_id', false, true)->nullable(true)->default(null)->index('index_opponent_user_id');
            $table->integer('side', false, true)->default(1)->comment('1 - хост играет крестиками, 0 - хост играет ноликами');
            $table->integer('move', false, true)->default(1)->comment('1 - ходит хост, 0 - ходит оппонент');
            $table->string('cell1', 1)->nullable(true)->default(null);
            $table->string('cell2', 1)->nullable(true)->default(null);
            $table->string('cell3', 1)->nullable(true)->default(null);
            $table->string('cell4', 1)->nullable(true)->default(null);
            $table->string('cell5', 1)->nullable(true)->default(null);
            $table->string('cell6', 1)->nullable(true)->default(null);
            $table->string('cell7', 1)->nullable(true)->default(null);
            $table->string('cell8', 1)->nullable(true)->default(null);
            $table->string('cell9', 1)->nullable(true)->default(null);
            $table->integer('status', false, true)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
