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
    protected function isListenerFired($event)
    {
        // Do not apply to the same or newer versions
        if (version_compare(static::VERSION, $event->old, '<=')) {
            return true;
        }

        return false;
    }
}
