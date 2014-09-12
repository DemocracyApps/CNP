<?php namespace DemocracyApps\CNP\Graph;

class DenizenSet extends \Eloquent
{
    protected $table = 'denizen_sets';

    protected $set = null;

    public function getDenizens() 
    {
        if (! $this->set) {
            $denizens = array();
            $idList = explode(',', $this->setSpecification);
            if ($idList && count($idList) > 0) {
                foreach ($idList as $id) {
                    $denizens[] = \DemocracyApps\CNP\Entities\Denizen::find($id);
                }
            }
            $this->set = $denizens;
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
    public function addDenizens(array $denizens)
    {
        if (!$this->set) $this->set = array();
        foreach ($denizens as $d) $this->set[$d->id] = $d;
        $this->type = 'list';
        $this->setSpecification = implode(',', $this->set);
        $this->generateSpec();
    }
    public function addDenizen($denizen)
    {
        if (! $this->set) $this->set = array();
        $this->set[$denizen->id] = $denizen;
        $this->type = 'list';
        $this->generateSpec();
    }
}