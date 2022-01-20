<?php

namespace Crater\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleEnabledEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $module;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($module)
    {
        $this->module = $module;
    }
}
