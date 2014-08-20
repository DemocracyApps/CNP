<?php
namespace DemocracyApps\CNP\Entities;

class Story extends Denizen 
{
    static    $classScapeId = -1;
    static $storyDenizenType = 0;
    protected $author;

    function __construct ($nm=null) {
        parent::__construct($nm, \CNP::getDenizenTypeId('Story'), self::$storyDenizenType);
    }

    static public function initialize() 
    {
        if (static::$classScapeId < 0) {
            static::$classScapeId = \CNP::getDenizenTypeId('Story');
        }
    }

}
