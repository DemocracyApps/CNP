<?php 

// Check whether we need any DB initializations
$rtinit = DemocracyApps\CNP\Entities\Eloquent\AppState::where('name','=', 'relationTypesInitialized')->get()->first();
if (! $rtinit) {
	\Log::info("No relationTypesInitialized");
}
else {
	\Log::info(var_dump($rtinit));
	dd($rtinit);
}

// We need to do some intializations on all the Denizen types
DemocracyApps\CNP\Entities\Person::initialize();
DemocracyApps\CNP\Entities\Story::initialize();

