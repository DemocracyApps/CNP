<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Utility\Api as Api;

class CollectorsController extends ApiController {
	protected $collector;

	function __construct (DAEntity\Eloquent\Collector $collector) 
	{
		$this->collector 			= $collector;
	}

	public function show ($id)
	{
		$collector = DAEntity\Eloquent\Collector::find($id);
		return \View::make('collectors.show', array('collector' => $collector));
	}

	public function uploadCollector()
	{
		$file = \Input::file('collector');
		if ($file) {
			$nm = $file->getClientOriginalName();
			//$file->move(public_path().'/collectors', 'test.json');

		    $contents = \File::get($file->getRealPath());
			return "Hello ".$nm . " at " . $contents;
		}
		else {
			return "Nothing";
		}
	}

	public function create() 
	{
    	\Session::put('CNP_RETURN_URL', \Request::server('HTTP_REFERER'));
    	\Log::info("Setting return URL to " . \Request::server('HTTP_REFERER'));
    	return \View::make('collectors.create', array('scape' => \Input::get('scape')));
	}

	public function store()
	{
		\Log::info("Top of collectors.store");
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
		}

        $this->collector->save();

        if ($isAPI) {
        }
        else {
			$returnURL = \Session::get('CNP_RETURN_URL');
			\Session::forget('CNP_RETURN_URL');
			if ( ! $returnURL) $returnURL = '/';
			\Log::info("Redirecting to " . $returnURL);
			return \Redirect::to($returnURL);
        }
	}

}
