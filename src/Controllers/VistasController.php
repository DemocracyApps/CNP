<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Utility\Api as Api;
use \DemocracyApps\CNP\Compositions\Outputs\Vista;
use \DemocracyApps\CNP\Entities\Denizen;

class VistasController extends ApiController {

	protected $vista;


	function __construct (Vista $vista)
	{
		$this->vista = $vista;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$vista = Vista::find(\Input::get('vista'));
		$typeList = null;
		$denizens = null;
		if ($vista->selector) {
			$typeList = array();
            $s = trim(preg_replace("([, ]+)", ' ', $vista->selector));
            if ($s) $types = explode(" ", $s);
            foreach ($types as $type) {
            	$typeList [] = \CNP::getDenizenTypeId($type);
            }
		}
        $s = trim(preg_replace("([, ]+)", ' ', $vista->input_composers));
        if ($s) $allowedComposers = explode(" ", $s);

        $page = \Input::get('page', 1);
        $pageLimit=\CNP::getConfigurationValue('pageLimit');
        $data = Denizen::getVistaDenizens ($vista->scape, $allowedComposers, $typeList, $page, $pageLimit);
        $denizens = \Paginator::make($data['items'], $data['total'], $pageLimit);

		$args = array('denizens' => $denizens, 'vista' => $vista);
		$args['composer'] = $vista->output_composer;
        return \View::make('vistas.index', array('denizens'=>$denizens, 'vista'=>$vista, 'composer'=>$vista->output_composer));
		return \View::make('vistas.index', $args);
	}


	/**
	 *
	 * @return Response
	 */
	public function create()
	{
    	return \View::make('vistas.create', array('scape' => \Input::get('scape')));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
		$params = [];
		if ($isAPI) {
			throw new \Exception("API Vista creation not implemented");
		}
		else {
			$data = \Input::all();
		}

        $rules = ['name'=>'required', 'output_composer'=>'required', 'input_composers'=>'required'];
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
        	if ($isAPI) {
        		return $this->respondFailedValidation(Api::compactMessages($validator->messages()));
        	}
        	else {
            	return \Redirect::back()->withInput()->withErrors($validator->messages());
            }
        }
	    
	    // Validation OK, let's create the vista

        $this->vista->name = $data['name'];
        $this->vista->scape = $data['scape'];
        $this->vista->input_composers = $data['input_composers'];
        $this->vista->output_composer = $data['output_composer'];
        if ($data['description']) $this->vista->description = $data['description'];

		// Now load in the file
        if (\Input::has('selector')) {
            $s = trim(preg_replace("([, ]+)", ' ', $data['selector']));
            if ($s) $s = explode(" ", $s);
            if ($s) $s = implode(",", $s);
            $this->vista->selector = $s;
        }
        $this->vista->save();

        if ($isAPI) {
        }
        else {
			return \Redirect::to('/scapes/'.$data['scape']);
        }
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
