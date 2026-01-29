<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuickMatchQueue extends Model
{
    protected $table = 'quick_match_queues';

    protected $fillable = [
        'queue_key',
        'user_id',
        'session_id',
        'status',
        'game_id',
        'side',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(ChessGame::class, 'game_id');
    }
}
