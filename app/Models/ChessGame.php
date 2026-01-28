<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChessGame extends Model
{
    protected $table = 'chess_games';

    protected $fillable = [
        'state',
        'status',
    ];

    protected $casts = [
        'state' => 'array',
    ];
}
