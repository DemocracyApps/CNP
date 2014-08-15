<?php
namespace DemocracyApps\CNP\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Cnp extends BaseFacade
{
/**
 * Get the registered name of the component.
 *
 * @return string
 */
    protected static function getFacadeAccessor()
    {
        return 'cnp';
    }
}