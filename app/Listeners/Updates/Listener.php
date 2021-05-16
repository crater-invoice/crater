<?php

namespace Crater\Listeners\Updates;

// Implementation taken from Akaunting - https://github.com/akaunting/akaunting
class Listener
{
    public const VERSION = '';

    /**
     * Check if should listen.
     *
     * @param  $event
     * @return bool
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
