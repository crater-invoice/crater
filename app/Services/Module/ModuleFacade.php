<?php

namespace Crater\Services\Module;

use Illuminate\Support\Facades\Facade as BaseFacade;

class ModuleFacade extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Module::class;
    }
}
