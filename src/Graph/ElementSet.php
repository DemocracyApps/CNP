<?php namespace DemocracyApps\CNP\Graph;

class ElementSet extends \Eloquent
{
    protected $table = 'element_sets';

    protected $set = null;

    public function getElements() 
    {
        if (! $this->set) {
            $elements = array();
            $idList = explode(',', $this->setSpecification);
            if ($idList && count($idList) > 0) {
                foreach ($idList as $id) {
                    $elements[] = \DemocracyApps\CNP\Entities\Element::find($id);
                }
            }
            $this->set = $elements;
        }
        return $this->set;
    }

    public function initialize() 
    {
        $this->userid = \Auth::user()->getId();
        $this->expires = date('Y-m-d H:i:s', time() + 24 * 60 * 60);
    }
    private function generateSpec()
    {
        $this->setSpecification = "";
        $glue = "";
        foreach($this->set as $item) {
            $this->setSpecification .= $glue . $item->id;
            $glue = ',';
        }
    }
    public function addElements(array $elements)
    {
        if (!$this->set) $this->set = array();
        foreach ($elements as $d) $this->set[$d->id] = $d;
        $this->type = 'list';
        $this->setSpecification = implode(',', $this->set);
        $this->generateSpec();
    }
    public function addElement($element)
    {
        if (! $this->set) $this->set = array();
        $this->set[$element->id] = $element;
        $this->type = 'list';
        $this->generateSpec();
    }
}