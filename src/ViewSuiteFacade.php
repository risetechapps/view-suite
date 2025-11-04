<?php

namespace RiseTechApps\ViewSuite;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RiseTechApps\ViewSuite\Skeleton\SkeletonClass
 */
class ViewSuiteFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'view-suite';
    }
}
