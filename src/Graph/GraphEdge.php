<?php namespace DemocracyApps\CNP\Graph;

class GraphEdge
{
    public $name = null;
    public $from = null;
    public $to   = null;

    function __construct (&$from, &$to, $name)
    {
        $this->from = $from;
        $this->to = $to;
        $this->name = $name;
    }
}