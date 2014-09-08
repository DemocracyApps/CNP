<?php
namespace DemocracyApps\CNP\Inputs;
use \DemocracyApps\CNP\Entities as DAEntity;

class DenizenGenerator 
{
    static $fcts = array(
        'Tag' => '\DemocracyApps\CNP\Inputs\DenizenGenerator::tagGenerator'
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
        if ($createdDenizens) {
            foreach ($createdDenizens as $d) {
                $d->scapeId = $scapeId;
            }
        }
        return $createdDenizens;
    }

    static public function registerElementGenerator ($elementType, $methodName) {
        self::$fcts[$elementType] = $methodName;
    }

    static private function tagGenerator($elementType, $content, $properties)
    {
        $createdDenizens = null;
        // We want to create separate tags for each word in the content;
        if ($content) {
            $tags = null;
            $s = trim(preg_replace("([, ]+)", ' ', $content));
            if ($s) $tags = explode(" ", $s);
            if ($tags && count($tags) > 0) {
                $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
                if (!class_exists($className)) throw new \Exception("Cannot find denizen class " . $className);
                $createdDenizens = array();
                foreach ($tags as $tag) {
                    $tag = strtolower($tag);
                    $d = DAEntity\Tag::findByName($tag);
                    if ( ! $d) {
                        $d = new $className($tag, \Auth::user()->getId());
                        $d->content = $tag;
                        if ($properties) {
                            foreach ($properties as $propName => $propValue) {
                                $d->setProperty($propName, $propValue);
                            }
                        }
                    }
                    $createdDenizens[] = $d;
                }

            }
        }
        // Must return an array of Denizens
        return $createdDenizens;
    }

}