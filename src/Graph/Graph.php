<?php namespace DemocracyApps\CNP\Graph;

class Graph 
{

    protected $nodeList = null;
    protected $maps = null; // adding a map is adding another way to get to the node (e.g., payload ID)

    public function dump()
    {
        dd($this->nodeList);
        dd($this->maps);
    }

    public function addNode($id, $payload)
    {
        if (! $this->nodeList) $this->nodeList = array();
        $this->nodeList[$id] = new GraphNode($id, $payload);
    }

    public function addEdge ($from, $to, $relation)
    {
        if (array_key_exists($from, $this->nodeList) && array_key_exists($to, $this->nodeList)) {
            $this->nodeList[$from]->addEdge($this->nodeList[$to], $relation);
        }
    }

    private function &getMap($name)
    {
        if (! $this->maps) $this->maps = array();
        if (! array_key_exists($name, $this->maps)) $this->maps[$name] = array();
        return $this->maps[$name];
    }

    /**
     * Assign an object to the payload, but also allow setting an alternate map
     * to the graph node using a key appropriate to the object (e.g., its own ID)
     */
    public function assignPayload($key, $object, $altkey = null, $mapName = null)
    {
        if (array_key_exists($key, $this->nodeList)) {
            $node = &$this->nodeList[$key];
            $node->assignPayload($object);
            if ($altkey) {
                $map = &$this->getMap($mapName);
                $map[$altkey] = $node;
            }
        }
    }
}