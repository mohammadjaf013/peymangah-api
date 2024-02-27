<?php

namespace Modules\Auth\App\Events;

use Illuminate\Queue\SerializesModels;

class OtpEvent
{
    use SerializesModels;


    public $mobile;
    public $otp;
    /**
     * Create a new event instance.
     */
    public function __construct($mobile,$otp)
    {
        $this->otp= $otp;
        $this->mobile= $mobile;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
