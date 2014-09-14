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
     * 
     * @param  string $key     Native key of graph
     * @param  object $object  The payload (e.g., denizen)
     * @param  string $altkey  Optional alternate key to use (e.g., denizen ID)
     * @param  string $mapName Name of map to store the alternate key in
     * @return boolean         True if we succeeded in finding a place to assign the payload
     */
    public function assignPayload($key, $object, $altkey = null, $mapName = null)
    {
        $result = false;
        if (array_key_exists($key, $this->nodeList)) {
            $node = &$this->nodeList[$key];
            $node->assignPayload($object);
            $result = true;
            if ($altkey) {
                $map = &$this->getMap($mapName);
                if (!array_key_exists($altkey, $map)) {
                    $map[$altkey] = array($node);
                }
                else {
                    $map[$altkey][] = $node;
                }
            }
        }
        return $result;
    }

    public function propagatePayloads($id, $mapName) {
        $node = null;
        if ($mapName) {
            $map = &$this->getMap($mapName);
            $node = &$map[$id][0];            
        }
        else {
            $node = $nodeList[$id];
        }
        
    }
}
