<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Analysis\Analysis;

class AnalysisController extends ApiController {
    protected $analysis;

    function __construct (Analysis $analysis)
    {
        $this->analysis	= $analysis;
    }

    public function show($id) {
        $analysis = Analysis::find($id);
        return \View::make('analysis.show', array('analysis' => $analysis));
    }

    public function create()
    {
        return \View::make('analysis.create', array('project' => \Input::get('project')));
    }

    public function store()
    {
        $data = \Input::all();
        $rules = ['name'=>'required'];
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withInput()->withErrors($validator->messages());
        }

        $this->analysis->name       = $data['name'];
        $this->analysis->project    = $data['project'];

        if ($data['notes']) $this->analysis->notes = $data['notes'];

        // Now load in the file
        if (\Input::hasFile('specification')) {
            $file = \Input::file('specification');
            $this->analysis->specification = \File::get($file->getRealPath());
            $str = json_minify($this->analysis->specification);
            $cfig = json_decode($str, true);
            if ( ! $cfig) {
                return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
            }
        }
        $this->analysis->save();

        return \Redirect::to('/admin/projects/'.$data['project']);

    }

    public function edit($id)
    {
        $analysis = Analysis::find($id);
        return \View::make('analysis.edit', array('analysis' => $analysis, 'fileerror' => null));
    }

    public function update($id)
    {
        $data = \Input::all();
        $rules = ['name'=>'required'];
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withInput()->withErrors($validator->messages());
        }

        $this->analysis = Analysis::find($id);
        $this->analysis->name       = $data['name'];
        $this->analysis->project    = $data['project'];

        if ($data['notes']) $this->analysis->notes = $data['notes'];

        // Now load in the file
        if (\Input::hasFile('specification')) {
            $file = \Input::file('specification');
            $this->analysis->specification = \File::get($file->getRealPath());
            $str = json_minify($this->analysis->specification);
            $cfig = json_decode($str, true);
            if ( ! $cfig) {
                return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
            }
        }
        $this->analysis->save();

        return \Redirect::to('/admin/analysis/'.$this->analysis->id);

    }

    public function destroy($id)
    {
        $analysis = Analysis::find($id);
        $projectId = $analysis->project;
        Analysis::delete($id);
        return \Redirect::to('/admin/projects/'.$projectId);
    }

}