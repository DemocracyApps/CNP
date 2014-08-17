<?php
namespace DemocracyApps\CNP\Utility;
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

        // Load information on scapes
        $arr = $cfg['denizenTypes'];
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
