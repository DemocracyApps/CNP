<?php namespace DemocracyApps\CNP\Compositions;

class Composition extends \Eloquent {

    public function createChildComposition()
    {
        $child = new Composition;
        $child->parent = $this->id;
        $child->input_composer_id = $this->input_composer_id;
        $child->save();
        return $child;
    }
}
