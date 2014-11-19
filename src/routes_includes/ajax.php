<?php 

class PP {

    public $label = null;
    public $value = null;
    public function __construct($label, $value) {
        $this->label = $label;
        $this->value = $value;
    }

}

Route::group(['prefix' => 'ajax'], function () 
    {
        Route::get('person', function()
        {
            $term = Input::get('term');
            $composer = \DemocracyApps\CNP\Compositions\Composer::find(\Input::get('composer'));
            $project = $composer->project;
            $list = DAEntity\Person::getElementsLike($project, $term);
            $ret = array();
            foreach ($list as $item) {
                $ret[] = new PP($item->name, $item->id);
            }
            return json_encode($ret);            
        });

        Route::get('setProjectDefaultInputComposer', '\DemocracyApps\CNP\Controllers\ProjectsController@setDefaultInputComposer');
        Route::get('setProjectDefaultOutputComposer', '\DemocracyApps\CNP\Controllers\ProjectsController@setDefaultOutputComposer');

        Route::get('curate', function()
        {
            $value = implode(':', \Input::all());

            return json_encode($value);
        });
    }
);
