<?php namespace DemocracyApps\CNP\Graph

class DenizenSet 
{
    $set = null;

    public function addDenizens(array $denizens)
    {
        if (! $set) $set = array();
        foreach ($denizens as $d) $set[$d->id] = $d;
    }
}