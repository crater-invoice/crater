<?php

namespace Laraspace\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateFinished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $alias;

    public $new;

    public $old;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($alias, $old, $new)
    {
        $this->alias = $alias;
        $this->old   = $old;
        $this->new   = $new;
    }
}
