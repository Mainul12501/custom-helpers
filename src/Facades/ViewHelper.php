<?php

namespace Mainul\CustomHelperFunctions\Facades;

use Illuminate\Support\Facades\Facade;

class ViewHelper extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Mainul\CustomHelperFunctions\Helpers\ViewHelper::class;
    }
}
