<?php

namespace DemocracyApps\CNP\Entities;

/**
 * 
 */
class Scape extends Denizen
{
    static      $classDenizenType = -1;
    protected   $user;

    function __construct ($nm = null, $userid = null) {
        parent::__construct($nm, $userid, static::$classDenizenType);
    }

 
    static public function initialize() 
    {
        if (static::$classDenizenType < 0) {
            static::$classDenizenType = \CNP::getDenizenTypeId('Scape');
        }
    }
}


