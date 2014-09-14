<?php namespace DemocracyApps\CNP\Graph;

class Graph 
{

    protected $nodeList = null;

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
}