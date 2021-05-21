<?php

namespace Crater\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UpdateFinished
{
    use Dispatchable;

    public $new;

    public $old;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($old, $new)
    {
        $this->old = $old;
        $this->new = $new;
    }
}
