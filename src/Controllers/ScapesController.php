<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;

class ScapesController extends ApiController {
	protected $scape;
	protected $scapeTransformer;

	function __construct (\DemocracyApps\CNP\Transformers\ScapeTransformer $scapeTransformer) 
	{
		$this->scapeTransformer = $scapeTransformer;
		//$this->beforeFilter('auth.basic', ['on' => 'post']);
	}
	/**
	 * List all scapes
	 * @return [] [description]
	 */
	public function index()
	{
		$scapes = DAEntity\Scape::all();
		return $this->respond([
			'data' => $this->scapeTransformer->transformCollection($scapes),
			'errors' => NULL
			]);
	}

	public function show ($id)
	{
		$scape = DAEntity\Scape::find($id);
		if (!$scape) {
			return $this->respondNotFound('Scape '.$id.' does not exist');
		}
		else {
			return $this->respond([
				'data' => $this->scapeTransformer->transform($scape)
				]);
		}
	}

	public function store()
	{
        if (\Auth::check()) {
        	dd('Hello');
        }
        else {
        	dd('Goodbye');
        }

		if ( ! \Input::get('name') or ! \Input::get('access'))
		{
			return $this->respondFailedValidation('Scape parameters failed validation');
		}
		// create the scape!!!
		return $this->respondCreated('Scape was successfully created');
	}

}
