<?php

namespace Fused\Zoom;

use Illuminate\Support\Facades\Facade;

class ZoomFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'zoom';
    }

}