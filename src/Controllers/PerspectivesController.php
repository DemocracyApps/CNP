<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Analysis\AnalysisOutput;
use \DemocracyApps\CNP\Analysis\AnalysisSet;
use \DemocracyApps\CNP\Analysis\AnalysisSetItem;
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
        $this->perspective->type       = "Unknown";
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
             * Now configure the type
             */
            $this->configurePerspectiveType($cfig['type'], $this->perspective);
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
             * Now configure the type
             */
            $this->configurePerspectiveType($cfig['type'], $this->perspective);

        }
        $this->perspective->save();

        return \Redirect::to('/admin/perspectives/'.$this->perspective->id);

    }

    private function configurePerspectiveType ($type, $perspective) {

        $perspectives = \CNP::getConfigurationValue('perspectives');
        $found = false;
        for ($i = 0; $i < sizeof($perspectives) && !$found; ++$i) {
            $p = $perspectives[$i];
            if ($p['name'] == $type) {
                $found = true;
                $perspective->type = $type;
                $perspective->requires_analysis = $p['requiresAnalysis'];
            }
        }
        if (! $found) {
            $perspective->type = "Unknown";
            $perspective->requires_analysis = false;
        }
    }

    public function destroy($id)
    {
        $perspective = Perspective::find($id);
        $projectId = $perspective->project;

        $analysisOutputs = AnalysisOutput::whereColumn('perspective', '=', $id);
        foreach ($analysisOutputs as $output) {
            $analysisSets = AnalysisSet::whereColumn('analysis_output', '=', $output->id);
            foreach($analysisSets as $set) {
                $items = AnalysisSetItem::whereColumn('analysis_set', '=', $set->id);
                foreach($items as $item) {
                    AnalysisSetItem::delete($item->id);
                }
                AnalysisSet::delete($set->id);
            }
            AnalysisOutput::delete($output->id);
        }

        Perspective::delete($id);
        return \Redirect::to('/admin/projects/'.$projectId);
    }

}