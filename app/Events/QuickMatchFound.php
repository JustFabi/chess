<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuickMatchFound implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $queueKey,
        public int $gameId,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new Channel('quick-match.'.$this->queueKey)];
    }

    public function broadcastAs(): string
    {
        return 'QuickMatchFound';
    }

    public function broadcastWith(): array
    {
        return [
            'gameId' => $this->gameId,
        ];
    }
}
