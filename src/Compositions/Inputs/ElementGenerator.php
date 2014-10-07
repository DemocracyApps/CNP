<?php
namespace DemocracyApps\CNP\Compositions\Inputs;
use \DemocracyApps\CNP\Entities as DAEntity;

class ElementGenerator 
{
    static $fcts = array(
        'Tag' => '\DemocracyApps\CNP\Compositions\Inputs\ElementGenerator::tagGenerator',
        'Person' => '\DemocracyApps\CNP\Compositions\Inputs\ElementGenerator::personGenerator'
        );

    /**
     * [generateElement description]
     * @param  [type] $elementType [description]
     * @param  [type] $content     [description]
     * @param  [type] $properties  [description]
     * @param  [type] $projectId     [description]
     * @return array               Array of elements
     */
    static public function generateElement ($elementType, $name, $content, $properties, $projectId)
    {
        $createdElements = null;
        if (array_key_exists($elementType, self::$fcts) && self::$fcts[$elementType]) {
            $createdElements = call_user_func(self::$fcts[$elementType], $name, $elementType, $content, $properties);
        }
        else {
            $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
            if (!class_exists($className)) throw new \Exception("Cannot find element class " . $className);
            $d = new $className($name, \Auth::user()->getId());
            $d->content = $content['value'];

            if ($properties) {
                foreach ($properties as $propName => $propValue) {
                    $d->setProperty($propName, $propValue);
                }
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

    static public function registerElementGenerator ($elementType, $methodName) {
        self::$fcts[$elementType] = $methodName;
    }

    static private function personGenerator($name, $elementType, $content, $properties)
    {
        $createdElements = null;
        // We want to create separate tags for each word in the content;
        if ($content) {
            $d = null;
            if ($content['isRef']) {
                $d = DAEntity\Person::find($content['id']);
            }
            else {
                $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
                if (!class_exists($className)) throw new \Exception("Cannot find element class " . $className);
                $d = new $className($name, \Auth::user()->getId());
                $d->content = $content['value'];
                if ($properties) {
                    foreach ($properties as $propName => $propValue) {
                        $d->setProperty($propName, $propValue);
                    }
                }
            }
            $createdElements = array($d);
        }
        // Must return an array of Elements
        return $createdElements;
    }

    static private function tagGenerator($name, $elementType, $content, $properties)
    {
        $createdElements = null;
        // We want to create separate tags for each word in the content;
        if ($content) {
            $tags = null;
            $s = trim($content['value']);
            if ($s) $tags = explode(",", $s);

            if ($tags && count($tags) > 0) {
                $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
                if (!class_exists($className)) throw new \Exception("Cannot find element class " . $className);
                $createdElements = array();
                foreach ($tags as $tag) {
                    $tag = trim(strtolower($tag));
                    if ($tag && strlen($tag) > 0) {
                        $d = DAEntity\Tag::findByName($tag);
                        if ( ! $d) {
                            $d = new $className($name, \Auth::user()->getId());

                            $d->content = $tag;
                            \Log::info("Created a tag with name " . $name . " and content " . $tag);                        
                            if ($properties) {
                                foreach ($properties as $propName => $propValue) {
                                    $d->setProperty($propName, $propValue);
                                }
                            }
                        }
                        $createdElements[] = $d;
                    }
                }

            }
        }
        // Must return an array of Elements
        return $createdElements;
    }

}