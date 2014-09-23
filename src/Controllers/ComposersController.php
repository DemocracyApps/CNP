<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Utility\Api as Api;
use \DemocracyApps\CNP\Compositions\Composer as Composer;

class ComposersController extends ApiController {
	protected $composer;

	function __construct (Composer $composer) 
	{
		$this->composer 			= $composer;
	}

	public function destroy ($id)
	{
		$composer = Composer::find($id);
		$scape = $composer->scape;
		$composer->delete();
		return \Redirect::to('/scapes/'.$scape);
	}

    public function index()
    {
        $composers = Composer::getUserComposers(\Auth::id());
        return \View::make('composers.index', array('composers' => $composers));
    }

	public function show ($id)
	{
		$composer = Composer::find($id);
		return \View::make('composers.show', array('composer' => $composer));
	}

	public function update($id) 
	{
		$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
		if ($isAPI) {
			throw new \Exception("API Composer update not yet implemented");
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

		$composer = Composer::find($id);
		$composer->name = $data['name'];
		if (\Input::has('description')) $composer->description = $data['description'];
        \Log::info("Test composer");        
		if (\Input::hasFile('composer')) {
            \Log::info("Yes, we have a composer");
            $ok = $this->loadComposerSpecification($composer, \Input::file('composer'));
            \Log::info("And OK = " . $ok);
			if ( ! $ok) {
				return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
			}
		}
        $composer->save();
        if ($isAPI) {
        }
        else {
			return \Redirect::to('/composers/'.$composer->id);
        }
	}

	public function edit($id) 
	{
		$composer = Composer::find($id);
    	return \View::make('composers.edit', array('scape' => \Input::get('scape'), 
    												'composer' => $composer,
    												'fileerror' => null));
	}

	public function create() 
	{
    	\Session::put('CNP_RETURN_URL', \Request::server('HTTP_REFERER'));
    	return \View::make('composers.create', array('scape' => \Input::get('scape')));
	}

    private function loadComposerSpecification($composer, $file)
    {
        $composer->specification = \File::get($file->getRealPath());
        $str = json_minify($composer->specification);
        $cfig = json_decode($str, true);
        if ( ! $cfig) {
            return false;
        }
        $composer->contains = null;
        if (array_key_exists('elements', $cfig)) {
            if ($composer->contains)
                $composer->contains .= ', elements';
            else
                $composer->contains .= 'elements';
        }
        if (array_key_exists('relations', $cfig)) {
            if ($composer->contains)
                $composer->contains .= ', relations';
            else
                $composer->contains .= 'relations';
        }
        if (array_key_exists('input', $cfig)) {
            if ($composer->contains)
                $composer->contains .= ', input';
            else
                $composer->contains .= 'input';
        }
        if (array_key_exists('output', $cfig)) {
            if ($composer->contains)
                $composer->contains .= ', output';
            else
                $composer->contains .= 'output';
        }
        $composer->dependson = null;
        if (array_key_exists('baseSpecificationId', $cfig))
            $composer->dependson = $cfig['baseSpecificationId'];
        else
            $composer->dependson = null;
        return true;
    }

	public function store()
	{
		$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
		$params = [];
		if ($isAPI) {
			throw new \Exception("API Composer creation not implemented");
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
	        // Validation OK, let's create the composer

        $this->composer->name = $data['name'];
        $this->composer->scape = $data['scape'];
        if ($data['description']) $this->composer->description = $data['description'];

		// Now load in the file
        if (\Input::hasFile('composer')) {
            $ok = $this->loadComposerSpecification($this->composer, \Input::file('composer'));
            if ( ! $ok) {
                return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
            }
        }
        $this->composer->save();

        if ($isAPI) {
        }
        else {
			$returnURL = \Session::get('CNP_RETURN_URL');
			\Session::forget('CNP_RETURN_URL');
			if ( ! $returnURL) $returnURL = '/';
			\Log::info("Redirecting to " . $returnURL);
			return \Redirect::to('/scapes/'.$data['scape']);
			return \Redirect::to($returnURL);
        }
	}

}
