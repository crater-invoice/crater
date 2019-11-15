<?php

namespace Crater\Listeners\Updates;

class Listener
{
    const VERSION = '';

    /**
     * Check if should listen.
     *
     * @param  $event
     * @return boolean
     */
    protected function check($event)
    {
        // Do not apply to the same or newer versions
        if (version_compare($event->old, static::VERSION, '>=')) {
            return false;
        }

        return true;
    }
}
