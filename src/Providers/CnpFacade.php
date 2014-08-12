<?php
namespace DemocracyApps\CNP\Providers;

use Illuminate\Support\Facades\Facade as BaseFacade;

class CnpFacade extends BaseFacade
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