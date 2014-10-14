<?php
namespace DemocracyApps\CNP\Compositions\Inputs;
use \DemocracyApps\CNP\Entities as DAEntity;

class ElementGenerator 
{
    static $fcts = array(
        'Tag' => '\DemocracyApps\CNP\Compositions\Inputs\ElementGenerator::tagGenerator',
        'Person' => '\DemocracyApps\CNP\Compositions\Inputs\ElementGenerator::personGenerator',
        'Organization' => '\DemocracyApps\CNP\Compositions\Inputs\ElementGenerator::organizationGenerator',
        'Group' => '\DemocracyApps\CNP\Compositions\Inputs\ElementGenerator::groupGenerator',
        'Place' => '\DemocracyApps\CNP\Compositions\Inputs\ElementGenerator::placeGenerator'
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
            if ($content['isRef']) {
                $d = DAEntity\Element::find($content['value']);
            }
            else {
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

    static private function groupGenerator($name, $elementType, $content, $properties)
    {
        $createdElements = null;
        if ($content) {
            $d = null;
            if ($content['isRef']) {
                $d = DAEntity\Group::find($content['id']);
            }
            else {
                $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
                if (!class_exists($className)) throw new \Exception("Cannot find element class " . $className);
                $d = DAEntity\Tag::findByContent($content['value']);
                if (! $d) {
                    $d = new $className($name, \Auth::user()->getId());
                    $d->content = $content['value'];
                }
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

    static private function placeGenerator($name, $elementType, $content, $properties)
    {
        $createdElements = null;
        if ($content) {
            $d = null;
            if ($content['isRef']) {
                $d = DAEntity\Place::find($content['id']);
            }
            else {
                $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
                if (!class_exists($className)) throw new \Exception("Cannot find element class " . $className);
                $d = DAEntity\Tag::findByContent($content['value']);
                if (! $d) {
                    $d = new $className($name, \Auth::user()->getId());
                    $d->content = $content['value'];
                }
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

    static private function organizationGenerator($name, $elementType, $content, $properties)
    {
        $createdElements = null;
        if ($content) {
            $d = null;
            if ($content['isRef']) {
                $d = DAEntity\Organization::find($content['id']);
            }
            else {
                $className = '\\DemocracyApps\\CNP\Entities\\'.$elementType;
                if (!class_exists($className)) throw new \Exception("Cannot find element class " . $className);
                $d = DAEntity\Tag::findByContent($content['value']);
                if (! $d) {
                    $d = new $className($name, \Auth::user()->getId());
                    $d->content = $content['value'];
                }
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
                        $d = DAEntity\Tag::findByContent($tag);
                        if ( ! $d) {
                            $d = new $className($name, \Auth::user()->getId());

                            $d->content = $tag;
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