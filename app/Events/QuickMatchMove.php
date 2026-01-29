<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuickMatchMove implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $gameId,
        public array $gameState,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new Channel('game.'.$this->gameId)];
    }

    public function broadcastAs(): string
    {
        return 'QuickMatchMove';
    }

    public function broadcastWith(): array
    {
        return [
            'gameId' => $this->gameId,
            'gameState' => $this->gameState,
        ];
    }
}
