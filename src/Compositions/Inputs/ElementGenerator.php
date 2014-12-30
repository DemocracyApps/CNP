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
            if ($content['isRef']) {
                $d = DAEntity\Element::find($content['value']);
            }
            else {

                /*
                 * $match indicates whether we should try to create shared references
                 * to elements with identical content, or should create unique instances.
                 */
                $match = true; // Need to do lookup by type here.
                if ($elementType == 'CnpComposition') $match = false;
                if (array_key_exists('match', $elementSpec)) {
                    $match = $elementSpec['match'];
                }

                $d = self::generateIt ($elementType, null, $match, $elementSpec, $name, $content['value'], $properties);

            }
            $createdElements = array($d);
        }

        return $createdElements;
    }

    static private function generateIt($elementType, $d, $match, $elementSpec, $name, $value, $properties)
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
        $elementTypeId = \CNP::getElementTypeId($elementType);

        if ($match && ! $d) {
            $d = \DemocracyApps\CNP\Entities\Element::findByContent($value, $elementTypeId);
        }
        if (! $d) {
            $d = new \DemocracyApps\CNP\Entities\Element($name, $elementTypeId);
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
                $d = DAEntity\Element::find($content['id']);
            }
            else {
                $d = new DAEntity\Element($name, CNP::getElementTypeId("Person"));
                $d = self::generateIt ($elementType, $d, true, $elementSpec, $name, $content['value'], $properties);

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
                $createdElements = array();
                foreach ($tags as $tag) {
                    $tag = trim($tag);

                    if ($tag && strlen($tag) > 0) {
                        $d = self::generateIt ($elementType, null, true, $elementSpec, $name, $tag, $properties);
                    }
                    $createdElements[] = $d;
                }
            }
        }
        // Must return an array of Elements
        return $createdElements;
    }

}