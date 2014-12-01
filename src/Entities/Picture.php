<?php
namespace DemocracyApps\CNP\Entities;

class Picture extends Element 
{
    static    $classElementType = -1;
    protected $author;

    function __construct ($nm = null, $userid = null) {
        parent::__construct($nm, $userid, static::$classElementType);
    }

    static public function initialize() 
    {
        if (static::$classElementType < 0) {
            static::$classElementType = \CNP::getElementTypeId('Picture');
        }
    }

}
