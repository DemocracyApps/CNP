<?php namespace DemocracyApps\CNP\Entities\Eloquent;

class Collection extends \Eloquent
{
    public $set = null;

    public static function boot()
    {
        parent::boot();
        static::saving(function ($collection) {
            return $collection->prepareToSave();
        });        
    }

    public function prepareToSave() {
        $this->set_json = json_encode($this->set);
        return true;
    }
    
    public function initialize() {
        if ($this->set == null && $this->set_json != null) {
            $this->set = json_decode($this->set_json);
        }
    }
}