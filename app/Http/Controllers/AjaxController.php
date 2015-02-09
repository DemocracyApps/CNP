<?php namespace DemocracyApps\CNP\Http\Controllers;

use DemocracyApps\CNP\Http\Requests;
use DemocracyApps\CNP\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AjaxController extends APIController {

	public function main($section, $page, $func, Request $request)
    {
        $className = "\\DemocracyApps\\CNP\\Ajax\\" . ucfirst($section) . "\\" . ucfirst($page). "Handler";

        $reflectionMethod = new \ReflectionMethod($className, 'handle');
        $response = $reflectionMethod->invokeArgs(null, array($func, $request));
        return $this->setStatusAndRespond($response);
    }

}
