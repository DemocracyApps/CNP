<?php
namespace DemocracyApps\CNP\Facades;

use Illuminate\Support\Facades\Facade;

class Cnp extends Facade
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