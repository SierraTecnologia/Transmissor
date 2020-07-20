<?php

namespace Transmissor\Facades;

use Illuminate\Support\Facades\Facade;

class Transmissor extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'transmissor';
    }
}
