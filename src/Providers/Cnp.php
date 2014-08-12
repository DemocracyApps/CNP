<?php
namespace DemocracyApps\CNP\Providers;
use DemocracyApps\CNP\Models as Cnpm;

use Illuminate\Container\Container;    

class Cnp {
    private $configuration = null;

    protected $scapesByName = array();
    protected $scapesById = array();

    /**
     * Container instance used to resolve classes.
     *
     * @var Illuminate\Container\Container
     */
    protected $container;

    /**
     * Allow a container instance to be set via constructor.
     *
     * @param mixed $container
     */
    public function __construct($container = null)
    {
        // If the container isn't provided...
        if (!$container instanceof Container) {
            // ... use an instance of the illuminate container.
            $container = new Container;
        }
        // Set the container property.
        $this->container = $container;
    }

    /**
     * Load values from the cnp.json configuration file
     * 
     */
    public function loadConfiguration ($cfg) {
        $this->configuration = $cfg;

        // Load information on scapes
        $arr = $cfg['scapes'];
        foreach ($arr as $scapeSpec) {
            $scape = new Cnpm\Scape ($scapeSpec['id'], $scapeSpec['name']);
            $this->scapesByName[strtolower($scape->getName())] = $scape;
            $this->scapesById[$scape->getId()] = $scape;
        }
    }

    /**
     *
     * Scape-related functions
     *
     */
    public function getScapeId ($name) {
        $nm = strtolower($name);
        if (array_key_exists($nm, $this->scapesByName)) {
            return $this->scapesByName[$nm]->getId();
        }
        throw new \OutOfBoundsException('Unknown scape ' . $name);
    }

    public function getScapeName ($id) {
        if (array_key_exists($id, $this->scapesById)) {
            return $this->scapesById[$id]->getName();
        }
        throw new \OutOfBoundsException('Unknown scape ' . $name);
    }
}
