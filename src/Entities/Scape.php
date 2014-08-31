<?php

namespace DemocracyApps\CNP\Entities;

/**
 * 
 */
class Scape extends Denizen
{
    static      $classDenizenType = -1;
    protected   $user;

    function __construct ($nm) {
        parent::__construct($nm, static::$classDenizenType);
    }

 
    static public function initialize() 
    {
        if (static::$classDenizenType < 0) {
            static::$classDenizenType = \CNP::getDenizenTypeId('Scape');
            \Log::info("Setting denizen type id to " . static::$classDenizenType);
        }
    }
}


