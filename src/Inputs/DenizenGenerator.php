<?php
namespace DemocracyApps\CNP\Inputs;
use \DemocracyApps\CNP\Entities as DAEntity;

class DenizenGenerator 
{
    static $fcts = array(
        'LTag' => '\DemocracyApps\CNP\Inputs\DenizenGenerator::tagGenerator'
        );

    /**
     * [generateDenizen description]
     * @param  [type] $elementType [description]
     * @param  [type] $content     [description]
     * @param  [type] $properties  [description]
     * @param  [type] $scapeId     [description]
     * @return array               Array of denizens
     */
    static public function generateDenizen ($elementType, $name, $content, $properties, $scapeId)
    {
        $createdDenizens = null;
        if (array_key_exists($elementType, self::$fcts) && self::$fcts[$elementType]) {
            $createdDenizens = call_user_func(self::$fcts[$elementType], $elementType, $content, $properties);
        }
        else {
            $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
            if (!class_exists($className)) throw new \Exception("Cannot find denizen class " . $className);
            $d = new $className($name, \Auth::user()->getId());
            $d->content = $content;
            if ($properties) {
                foreach ($properties as $propName => $propValue) {
                    $d->setProperty($propName, $propValue);
                }
            }
            $createdDenizens = array($d);
        }
        foreach ($createdDenizens as $d) {
            $d->scapeId = $scapeId;
        }
        return $createdDenizens;
    }

    static public function registerElementGenerator ($elementType, $methodName) {
        self::$fcts[$elementType] = $methodName;
    }

    static private function tagGenerator($elementType, $content, $properties)
    {
        // Must return an array of Denizens
        dd("Special processing of " . $elementType);
    }

}