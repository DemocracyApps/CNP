<?php
namespace DemocracyApps\CNP\Compositions\Inputs;
use \DemocracyApps\CNP\Entities as DAEntity;

class ElementGenerator 
{
    static $fcts = array(
        'Tag' => '\DemocracyApps\CNP\Compositions\Inputs\ElementGenerator::tagGenerator',
        'Person' => '\DemocracyApps\CNP\Compositions\Inputs\ElementGenerator::personGenerator'
        );

    static public function generateElement ($elementSpec, $name, $content, $properties, $projectId)
    {
        $elementType = $elementSpec['type'];

        $createdElements = null;
        if (array_key_exists($elementType, self::$fcts) && self::$fcts[$elementType]) {
            $createdElements = call_user_func(self::$fcts[$elementType], $elementSpec, $name, $elementType, $content, $properties);
        }
        else {
            $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
            if (!class_exists($className)) throw new \Exception("Cannot find element class " . $className);
            if ($content['isRef']) {
                $d = DAEntity\Element::find($content['value']);
            }
            else {
                $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
                if (!class_exists($className)) throw new \Exception("Cannot find element class " . $className);

                $match = true; // Need to do lookup by type here.
                if ($elementType == 'CnpComposition') $match = false;
                if (array_key_exists('match', $elementSpec)) {
                    $match = $elementSpec['match'];
                }

                $d = self::generateIt ($className, null, $match, $elementSpec, $name, $content['value'], $properties);

            }
            $createdElements = array($d);
        }
        if ($createdElements) {
            foreach ($createdElements as $d) {
                $d->projectId = $projectId;
            }
        }
        return $createdElements;
    }

    static private function generateIt($className, $d, $match, $elementSpec, $name, $value, $properties)
    {
        // Transform the content
        if (array_key_exists('transform', $elementSpec)) {
            $transforms = explode(':', $elementSpec['transform']);
            foreach ($transforms as $transform) {
                switch($transform) {
                    case "uc":
                        $value = strtoupper($value);
                        break;
                    case "lc":
                        $value = strtolower($value);
                        break;
                    case "ucfirst":
                        $value = ucfirst($value);
                        break;
                    case "ucwords":
                        $value = ucwords($value);
                        break;
                    default:
                        break; // Nothing
                }
            }
        }
        if ($match && ! $d) {
            $method = new \ReflectionMethod($className, 'findByContent');
            $d = $method->invoke(null, $value);
        }
        if (! $d) {
            $d = new $className($name, \Auth::user()->getId());
            $d->content = $value;
        }
        
        if ($properties) {
            foreach ($properties as $propName => $propValue) {
                $d->setProperty($propName, $propValue);
            }
        }
        return $d;
    }

    static public function registerElementGenerator ($elementType, $methodName) 
    {
        self::$fcts[$elementType] = $methodName;
    }

    static private function personGenerator($elementSpec, $name, $elementType, $content, $properties)
    {
        $createdElements = null;
        if ($content) {
            $d = null;
            if ($content['isRef']) {
                $d = DAEntity\Person::find($content['id']);
            }
            else {
                $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
                if (!class_exists($className)) throw new \Exception("Cannot find element class " . $className);

                $d = new $className($name, \Auth::user()->getId());
                $d = self::generateIt ($className, $d, true, $elementSpec, $name, $content['value'], $properties);

            }
            $createdElements = array($d);
        }
        // Must return an array of Elements
        return $createdElements;
    }

    static private function tagGenerator($elementSpec, $name, $elementType, $content, $properties)
    {
        $createdElements = null;
        if ($content) {
            $tags = null;
            $s = trim($content['value']);
            if ($s) $tags = explode(",", $s);

            if ($tags && count($tags) > 0) {
                $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
                if (!class_exists($className)) throw new \Exception("Cannot find element class " . $className);
                $createdElements = array();
                foreach ($tags as $tag) {
                    $tag = trim($tag);

                    if ($tag && strlen($tag) > 0) {
                        $d = self::generateIt ($className, null, true, $elementSpec, $name, $tag, $properties);
                    }
                    $createdElements[] = $d;
                }
            }
        }
        // Must return an array of Elements
        return $createdElements;
    }

}