<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Utility\Api as Api;

class CollectorsController extends ApiController {
	protected $collector;

	function __construct (DAEntity\Eloquent\Collector $collector) 
	{
		$this->collector 			= $collector;
	}

	public function destroy ($id)
	{
		$collector = DAEntity\Eloquent\Collector::find($id);
		$scape = $collector->scape;
		$collector->delete();
		return \Redirect::to('/scapes/'.$scape);
	}

	public function show ($id)
	{
		$collector = DAEntity\Eloquent\Collector::find($id);
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

		$collector = DAEntity\Eloquent\Collector::find($id);
		$collector->name = $data['name'];
		if (\Input::has('description')) $collector->description = $data['description'];
		if (\Input::hasFile('specification')) {
			$file = \Input::file('specification');
		    $collector->specification = \File::get($file->getRealPath());
			$str = json_minify($collector->specification);
			$cfig = json_decode($str, true);
			if ( ! $cfig) {
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
		$collector = DAEntity\Eloquent\Collector::find($id);
    	return \View::make('collectors.edit', array('scape' => \Input::get('scape'), 
    												'collector' => $collector,
    												'fileerror' => null));
	}

	public function create() 
	{
    	\Session::put('CNP_RETURN_URL', \Request::server('HTTP_REFERER'));
    	return \View::make('collectors.create', array('scape' => \Input::get('scape')));
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
		$file = \Input::file('collector');
		if ($file) {
			$nm = $file->getClientOriginalName();
			//$file->move(public_path().'/collectors', 'test.json');
		    $this->collector->specification = \File::get($file->getRealPath());
			$str = json_minify($this->collector->specification);
			$cfig = json_decode($str, true);
			if ( ! $cfig) {
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
