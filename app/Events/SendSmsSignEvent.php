<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Contract\Models\ContractModel;
use Modules\Contract\Models\Receipt\ReceiptModel;

class SendSmsSignEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $contract;
    public $user;
    public $singer;

    public function __construct( $contract,$user,$singer)
    {
        $this->contract = $contract;
        $this->user = $user;
        $this->singer= $singer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
