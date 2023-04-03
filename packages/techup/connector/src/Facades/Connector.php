<?php

namespace Techup\Connector\Facades;

use Illuminate\Support\Facades\Facade;

class Connector extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'connector';
    }
}
