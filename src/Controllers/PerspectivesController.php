<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Analysis\Perspective;

class PerspectivesController extends ApiController {
    protected $perspective;

    function __construct (Perspective $perspective)
    {
        $this->perspective	= $perspective;
    }

    public function show($id) {
        $perspective = Perspective::find($id);
        return \View::make('perspective.show', array('perspective' => $perspective));
    }

    public function create()
    {
        return \View::make('perspective.create', array('project' => \Input::get('project')));
    }

    public function store()
    {
        $data = \Input::all();
        $rules = ['name'=>'required'];
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withInput()->withErrors($validator->messages());
        }

        $this->perspective->name       = $data['name'];
        $this->perspective->project    = $data['project'];
        $this->perspective->type       = "None";
        $this->perspective->requires_analysis = false;

        if ($data['notes']) $this->perspective->notes = $data['notes'];

        // Now load in the file
        if (\Input::hasFile('specification')) {
            $file = \Input::file('specification');
            $this->perspective->specification = \File::get($file->getRealPath());
            $str = json_minify($this->perspective->specification);
            $cfig = json_decode($str, true);
            if ( ! $cfig) {
                return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
            }

            /*
             * Now get the type
             */
            $type = $cfig['type'];
            $perspectives = \CNP::getConfigurationValue('perspectives');
            dd($perspectives);
        }
        $this->perspective->last = date('Y-m-d H:i:s', time() - 24 * 60 * 60); // We only care that it's strictly before updated time.
        $this->perspective->save();

        return \Redirect::to('/admin/projects/'.$data['project']);

    }

    public function edit($id)
    {
        $perspective = Perspective::find($id);
        return \View::make('perspective.edit', array('perspective' => $perspective, 'fileerror' => null));
    }

    public function update($id)
    {
        $data = \Input::all();
        $rules = ['name'=>'required'];
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withInput()->withErrors($validator->messages());
        }

        $this->perspective = Perspective::find($id);
        $this->perspective->name       = $data['name'];
        $this->perspective->project    = $data['project'];

        if ($data['notes']) $this->perspective->notes = $data['notes'];

        // Now load in the file
        if (\Input::hasFile('specification')) {
            $file = \Input::file('specification');
            $this->perspective->specification = \File::get($file->getRealPath());
            $str = json_minify($this->perspective->specification);
            $cfig = json_decode($str, true);
            if ( ! $cfig) {
                return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
            }
            /*
             * Now get the type
             */
            $type = $cfig['type'];

            dd($type);
        }
        $this->perspective->save();

        return \Redirect::to('/admin/perspective/'.$this->perspective->id);

    }

    public function destroy($id)
    {
        $analysis = Perspective::find($id);
        $projectId = $analysis->project;
        Perspective::delete($id);
        return \Redirect::to('/admin/projects/'.$projectId);
    }

}