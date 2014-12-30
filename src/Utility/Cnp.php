<?php
namespace DemocracyApps\CNP\Utility;
use DemocracyApps\CNP\Entities as Cnpm;

use Illuminate\Container\Container;    

class Cnp {
    private $configuration = null;

    protected $elementTypesByName = null;
    protected $elementTypesById = null;

    /**
     * Container instance used to resolve classes.
     *
     * @var Illuminate\Container\Container
     */
    protected $app;

    /**
     * Allow a container instance to be set via constructor.
     *
     * @param mixed $app
     */
    public function __construct($app = null)
    {
        // If the container isn't provided...
        if (!$app instanceof Container) {
            // ... use an instance of the illuminate container.
            $app = new Container;
        }
        // Set the app property.
        $this->app = $app;
    }

    /**
     * Load values from the cnp.json configuration file
     * 
     */
    public function loadConfiguration ($cfg) {
        $this->configuration = $cfg;

        // Load information on ElementTypes
//        $arr = $cfg['elementTypes'];
//        foreach ($arr as $elementTypeSpec) {
//            $elementType = new Cnpm\ElementType ($elementTypeSpec['id'], $elementTypeSpec['name']);
//            $this->elementTypesByName[strtolower($elementType->getName())] = $elementType;
//            $this->elementTypesById[$elementType->getId()] = $elementType;
//        }
    }

    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     *
     * ElementType-related functions
     *
     */

    private function initializeElementTypes() {
            $this->elementTypesById = array();
            $this->elementTypesByName = array();
            $et = Cnpm\Eloquent\ElementType::all();
            foreach ($et as $eItem) {
                $this->elementTypesByName[$eItem->name] = $eItem;
                $this->elementTypesById[$eItem->id] = $eItem;
            }
    }
    public function getElementTypeId ($name) {
        if ($this->elementTypesByName === null) {
            $this->initializeElementTypes();
        }
//        dd($this->elementTypesByName);
//        $nm = strtolower($name);
        $nm= $name;
        if (array_key_exists($nm, $this->elementTypesByName)) {
            return $this->elementTypesByName[$nm]->getId();
        }
        throw new \OutOfBoundsException('Unknown Element Type ' . $name);
    }

    public function getElementTypeName ($id) {
        if ($this->elementTypesByName === null) {
            $this->initializeElementTypes();
        }

        if (array_key_exists($id, $this->elementTypesById)) {
            return $this->elementTypesById[$id]->getName();
        }
        throw new \OutOfBoundsException('Unknown Element Type ' . $name);
    }

    public function getElementTypes ($asHash)
    {
        if ($this->elementTypesByName === null) {
            $this->initializeElementTypes();
        }
        $result = array();
        foreach ($this->elementTypesById as $type) {
            if ($asHash) {
                $result[$type->id] = $type->name;
            }
            else {
                $result[] = $type->name;
            }
        }
        return $result;
    }

    /**
     * @string $name [Parameter name]
     *
     * @return  string or array
     */
    public function getConfigurationValue ($name) 
    {
        return $this->configuration[$name];
    }
}
