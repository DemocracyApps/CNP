<?php namespace DemocracyApps\CNP\Graph;

class GraphNode
{
    public $id = null;
    protected $payload = null;
    protected $edges = null;

    function __construct($id, $payload) 
    {
        $this->id = $id;
        $this->payload = $payload;
    }

    public function addEdge (&$to, $relation)
    {
        if (! $this->edges) $this->edges = array();
        $this->edges[] = new GraphEdge($this, $to, $relation);
    }

    public function assignPayload ($p)
    {
        $this->payload = $p;
    }
}