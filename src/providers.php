<?php

use DemocracyApps\CNP\Entities as Cnpm;

$fileName = base_path()."/src/cnp.json";
$str = file_get_contents($fileName);
$str = json_minify($str);
$cfig = json_decode($str, true);
CNP::loadConfiguration($cfig);

