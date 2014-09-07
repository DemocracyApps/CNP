<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Utility\Api as Api;
use \DemocracyApps\CNP\Inputs\Collector as Collector;

class CollectorsController extends ApiController {
	protected $collector;

	function __construct (Collector $collector) 
	{
		$this->collector 			= $collector;
	}

	public function destroy ($id)
	{
		$collector = Collector::find($id);
		$scape = $collector->scape;
		$collector->delete();
		return \Redirect::to('/scapes/'.$scape);
	}

	public function show ($id)
	{
		$collector = Collector::find($id);
		return \View::make('collectors.show', array('collector' => $collector));
	}

	public function update($id) 
	{
		$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
		if ($isAPI) {
			throw new \Exception("API Collector update not yet implemented");
		}
		else {
			$data = \Input::all();
		}
        $rules = ['name'=>'required'];
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
        	if ($isAPI) {
        		return $this->respondFailedValidation(Api::compactMessages($validator->messages()));
        	}
        	else {
            	return \Redirect::back()->withInput()->with('fileerror', null)->withErrors($validator->messages());
            }
        }

		$collector = Collector::find($id);
		$collector->name = $data['name'];
		if (\Input::has('description')) $collector->description = $data['description'];
        \Log::info("Test collector");        
		if (\Input::hasFile('collector')) {
            \Log::info("Yes, we have a collector");
            $ok = $this->loadCollectorCsvSpecification($collector, \Input::file('collector'));
            \Log::info("And OK = " . $ok);
			if ( ! $ok) {
				return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
			}
		}
        $collector->save();
        if ($isAPI) {
        }
        else {
			return \Redirect::to('/collectors/'.$collector->id);
        }
	}

	public function edit($id) 
	{
		$collector = Collector::find($id);
    	return \View::make('collectors.edit', array('scape' => \Input::get('scape'), 
    												'collector' => $collector,
    												'fileerror' => null));
	}

	public function create() 
	{
    	\Session::put('CNP_RETURN_URL', \Request::server('HTTP_REFERER'));
    	return \View::make('collectors.create', array('scape' => \Input::get('scape')));
	}

    private function loadCollectorCsvSpecification($collector, $file)
    {
        $collector->specification = \File::get($file->getRealPath());
        $str = json_minify($collector->specification);
        $cfig = json_decode($str, true);
        if ( ! $cfig) {
            return false;
        }
        $collector->contains = null;
        if (array_key_exists('elements', $cfig)) {
            if ($collector->contains)
                $collector->contains .= ', elements';
            else
                $collector->contains .= 'elements';
        }
        if (array_key_exists('relations', $cfig)) {
            if ($collector->contains)
                $collector->contains .= ', relations';
            else
                $collector->contains .= 'relations';
        }
        if (array_key_exists('input', $cfig)) {
            if ($collector->contains)
                $collector->contains .= ', input';
            else
                $collector->contains .= 'input';
        }
        $collector->dependson = null;
        if (array_key_exists('baseSpecificationId', $cfig))
            $collector->dependson = $cfig['baseSpecificationId'];
        else
            $collector->dependson = null;
        return true;
    }

	public function store()
	{
		$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
		$params = [];
		if ($isAPI) {
			throw new \Exception("API Collector creation not yet implemented");
		}
		else {
			$data = \Input::all();
		}

        $rules = ['name'=>'required'];
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
        	if ($isAPI) {
        		return $this->respondFailedValidation(Api::compactMessages($validator->messages()));
        	}
        	else {
            	return \Redirect::back()->withInput()->withErrors($validator->messages());
            }
        }
	        // Validation OK, let's create the collector

        $this->collector->name = $data['name'];
        $this->collector->scape = $data['scape'];
        if ($data['description']) $this->collector->description = $data['description'];

		// Now load in the file
        if (\Input::hasFile('collector')) {
            $ok = $this->loadCollectorCsvSpecification($this->collector, \Input::file('collector'));
            if ( ! $ok) {
                return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
            }
        }
        $this->collector->save();

        if ($isAPI) {
        }
        else {
			$returnURL = \Session::get('CNP_RETURN_URL');
			\Session::forget('CNP_RETURN_URL');
			if ( ! $returnURL) $returnURL = '/';
			\Log::info("Redirecting to " . $returnURL);
			return \Redirect::to('/collectors/'.$this->collector->id);
			return \Redirect::to($returnURL);
        }
	}

}
