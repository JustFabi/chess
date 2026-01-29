<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quick_match_queues', function (Blueprint $table) {
            $table->id();
            $table->uuid('queue_key')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id');
            $table->string('status')->default('waiting');
            $table->foreignId('game_id')->nullable()->constrained('chess_games')->nullOnDelete();
            $table->string('side')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_match_queues');
    }
};
