<?php

namespace DemocracyApps\CNP\Entities;

/**
 * 
 */
class Scape extends Denizen
{
    static      $classScapeId = -1;
    static      $userDenizenType = 0;
    protected   $user;

    function __construct ($nm) {
        parent::__construct($nm, static::$classScapeId, 0);
    }

 
    static public function initialize() 
    {
        if (static::$classScapeId < 0) {
            static::$classScapeId = \CNP::getDenizenTypeId('Scape');
        }
    }
}


