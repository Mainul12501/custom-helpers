<?php

namespace Mainul\CustomHelperFunctions\Facades;

use Illuminate\Support\Facades\Facade;

class CustomHelper extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
//        return \Mainul\CustomHelperFunctions\Helpers\ViewHelper::class;
        return \Mainul\CustomHelperFunctions\Helpers\CustomHelper::class;
    }
}
