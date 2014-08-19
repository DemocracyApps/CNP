<?php

namespace DemocracyApps\CNP\Controllers;

class MapController extends BaseController {

    public function show() {
        return \View::make('showmap');
    }

    public function test() {
    	$start = array(35.58, -82.5558);
    	$markers = null;
    	return \View::make('sample')->with(array('start' => $start, 'markers' => $markers));
    	//return \View::make('sample')->with('start', $start);

    }
}
