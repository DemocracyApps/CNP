<?php

namespace DemocracyApps\CNP\Entities;

/**
 * 
 */
class Scape extends Element
{
    static      $classElementType = -1;
    protected   $user;

    function __construct ($nm = null, $userid = null) {
        parent::__construct($nm, $userid, static::$classElementType);
    }

 
    static public function initialize() 
    {
        if (static::$classElementType < 0) {
            static::$classElementType = \CNP::getElementTypeId('Scape');
        }
    }
}


