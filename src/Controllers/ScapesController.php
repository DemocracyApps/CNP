<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Utility\Api as Api;

class ScapesController extends ApiController {
	protected $scape;
	protected $scapeTransformer;

	function __construct (DAEntity\Scape $scape, 
						  \DemocracyApps\CNP\Transformers\ScapeTransformer $scapeTransformer) 
	{
		$this->scape 			= $scape;
		$this->scapeTransformer = $scapeTransformer;
	}
	/**
	 * List all scapes
	 * @return [] [description]
	 */
	public function index()
	{
    	$scapes = DAEntity\Scape::allUserDenizens(\Auth::id());
    	$data = $this->scapeTransformer->transformCollection($scapes);
		return $this->respondIndex('List of API user scapes', $data);
	}

	public function show ($id)
	{
		$scape = DAEntity\Scape::find($id);
		$collectors = DAEntity\Eloquent\Collector::where('scape', '=', $id)->get();
		$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
		if ($isAPI) {
			if (!$scape) {
				return $this->respondNotFound('Scape '.$id.' does not exist');
			}
			else {
				$data = $this->scapeTransformer->transform($scape);
				return $this->respondIndex('Requested scape', $data);
			}
		}
		else {
			return \View::make('scapes.show', array('scape' => $scape, 'collectors' => $collectors));
		}
	}

	public function create() 
	{
    	\Session::put('CNP_RETURN_URL', \Request::server('HTTP_REFERER'));
    	return \View::make('scapes.create');
	}

	public function store()
	{
		$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
		$params = [];
		if ($isAPI) {
			if (\Input::json() && sizeof(\Input::json()->all()) > 0) {
				$data = \Input::json()->get('data');
				$params = \Input::json()->get('params');
			}
			else {
				return $this->respondFormatError('Empty or invalid JSON body');
			}
		}
		else {
			$data = \Input::all();
		}
		$multi = array_key_exists('multi', $params)?$params['multi']:false;

		if (! $multi) {
	        $rules = ['name'=>'required', 'access'=>'required'];
	        $validator = \Validator::make($data, $rules);
	        if ($validator->fails()) {
	        	if ($isAPI) {
	        		return $this->respondFailedValidation(Api::compactMessages($validator->messages()));
	        	}
	        	else {
	            	return \Redirect::back()->withInput()->withErrors($validator->messages());
	            }
	        }
	        // Validation OK, let's create the scape
	        $user = DAEntity\Eloquent\User::find(\Auth::user()->getId());

	        $this->scape->setName($data['name']);
	        $this->scape->setProperty('access', $data['access']);
	        if ($data['content']) $this->scape->setContent($data['content']);
	        $this->scape->setUserId($user->getId());
	        $this->scape->save();

	        // Now let's create the relations with the creator Person
	        $person = DAEntity\Person::find($user->getDenizenId());
	        $relations = DAEntity\Relation::createRelationPair($person->getId(), $this->scape->getId(),
	                                                          "CreatorOf");
	        foreach($relations as $relation) {
	            $relation->save();
	        }

	        if ($isAPI) {
				$data = $this->scapeTransformer->transform($this->scape);
				return $this->respondCreated('Scape was successfully created', $data);	        	
	        }
	        else {
    			$returnURL = \Session::get('CNP_RETURN_URL');
    			\Session::forget('CNP_RETURN_URL');
    			if ( ! $returnURL) $returnURL = '/';
    			return \Redirect::to($returnURL);
	        }
		}
		else {
			throw new \Exception("Scapes multi store not yet implemented");
		}
	}

}
