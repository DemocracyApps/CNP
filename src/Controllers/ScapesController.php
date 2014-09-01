<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;

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

	public function create() 
	{
    	\Session::put('CNP_RETURN_URL', \Request::server('HTTP_REFERER'));
    	//dd(\Request::instance());
    	return \View::make('scapes.create');
	}

	private static function isApiCall ($uri)
	{
		$pos = strpos($uri, '/api');
		\Log::info("The uri is ".$uri." and the pos is " . $pos);
		$isAPI = false;
		if ($pos !== false) {
			$isAPI = true;
		}
		return $isAPI;
	}

	public function store()
	{
		$isAPI = self::isApiCall(\Request::server('REQUEST_URI'));
		$params = [];
		if ($isAPI) {
			if (\Input::json() && sizeof(\Input::json()->all()) > 0) {
				\Log::info(\Input::json()->all());
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
	        		return $this->respondFailedValidation(self::compactMessages($validator->messages()));
	        		//return $this->respondFailedValidation($msgs);
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
				return $this->respondCreated('Scape was successfully created');	        	
	        }
	        else {
    			$returnURL = \Session::get('CNP_RETURN_URL');
    			\Log::info("Let's move back to ".$returnURL);
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
