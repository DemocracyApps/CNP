<?php

namespace DemocracyApps\CNP\Controllers;

class MapController extends BaseController {

    public function show() {
        return \View::make('showmap');
    }

    public function test() {

    	\JavaScript::put([
    		'foo' => 'bar',
    		'coords' => array(35.58, -82.5558),
    		'start' => array(35.58, -82.5558)
    		]);
    	$start = array(35.58, -82.5558);
    	$markers = null;

    	return \View::make('sample')->with(array('start' => $start, 'markers' => $markers));
    	//return \View::make('sample')->with('start', $start);

    }
}
