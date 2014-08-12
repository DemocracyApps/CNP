<?php

use DemocracyApps\CNP\Models as Cnpm;

$str = file_get_contents("../src/cnp.json");

$str = json_minify($str);

$cfig = json_decode($str, true);
CNP::loadConfiguration($cfig);

//dd(json_last_error());
//dd($cfig);

//$scapes = $cfig['scapes'];

//$wd = getcwd();

//CNP::registerScape('place', new Cnpm\Scape(10, 'Place'));
//CNP::registerScape('people', new Cnpm\Scape(20, 'People'));
//CNP::registerScape('story', new Cnpm\Scape($scapes[2]['id'], 'Story'));

