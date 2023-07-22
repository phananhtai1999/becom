<?php

namespace Techup\Mailbox\Facades;

use Illuminate\Support\Facades\Facade;

class Mailbox extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'mailbox';
    }
}
