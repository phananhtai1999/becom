<?php

namespace Techup\SiteController\Facades;

use Illuminate\Support\Facades\Facade;

class SiteController extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'site_controller';
    }
}
