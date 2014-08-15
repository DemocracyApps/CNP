<?php
namespace DemocracyApps\CNP\Models;

class Story extends Denizen 
{
    static    $classScapeId = -1;
    static $storyDenizenType = 0;
    protected $author;

    function __construct ($nm=null) {
        parent::__construct($nm, \CNP::getScapeId('Story'), self::$storyDenizenType);
    }

    static public function initialize() 
    {
        if (static::$classScapeId < 0) {
            static::$classScapeId = \CNP::getScapeId('Story');
        }
    }

}
