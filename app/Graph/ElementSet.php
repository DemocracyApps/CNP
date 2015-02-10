<?php namespace DemocracyApps\CNP\Graph;

use DemocracyApps\CNP\Utility\TableBackedObject;

class ElementSet extends TableBackedObject
{
    static  $tableName = 'element_sets';
    static protected $tableFields = array('userid', 'expires', 'type', 'setSpecification',
        'created_at', 'updated_at');

    public $userid = null;
    public $expires = null;
    public $type = null;
    public $setSpecification = null;
    public $created_at = null;
    public $updated_at = null;


    protected $set = null;

    /**
     * @return array|null
     */
    public function getElements()
    {
        if (! $this->set) {
            $elements = array();
            $idList = explode(',', $this->setSpecification);
            if ($idList && count($idList) > 0) {
                foreach ($idList as $id) {
                    $elements[] = Element::find($id);
                }
            }
            $this->set = $elements;
        }
        return $this->set;
    }

    public function initialize() 
    {
        $this->userid = \Auth::user()->id;
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