<?php 

use DemocracyApps\CNP\Entities\Eloquent\AppState;

// Assumes that providers.php has already been called.

// Check whether we need any DB initializations
if (Schema::hasTable('app_state') && Schema::hasTable('relation_types')) {

	$rtinit = AppState::where('name','=', 'relationTypesInitialized')->get()->first();
	if (! $rtinit) {
		\Log::info("Initializing relationTypes");
		$fileName = base_path()."/src/Entities/entities.json";
		$str = file_get_contents($fileName);
		$str = json_minify($str);
		$cfig = json_decode($str, true);
		DemocracyApps\CNP\Entities\Eloquent\RelationType::initDB($cfig);
		$rtinit = new DemocracyApps\CNP\Entities\Eloquent\AppState;
		$rtinit->name = 'relationTypesInitialized';
		$rtinit->value = '1';
		$rtinit->save();
	}
}

// We need to do some intializations on all the Element types
DemocracyApps\CNP\Entities\CnpComposition::initialize();
DemocracyApps\CNP\Entities\Person::initialize();
DemocracyApps\CNP\Entities\Story::initialize();
DemocracyApps\CNP\Entities\Tag::initialize();
DemocracyApps\CNP\Entities\StoryElement::initialize();

$mode = Input::get('mode');
if ($mode) {
	Session::put('cnpMode', $mode);
}
else {
	Session::put('cnpMode', 'app');
}


