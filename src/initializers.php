<?php 

use DemocracyApps\CNP\Entities\Eloquent\AppState;

// Assumes that providers.php has already been called.

// Check whether we need any DB initializations
if (Schema::hasTable('app_state')) {
	if (Schema::hasTable('relation_types')) {

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
	if (Schema::hasTable('users')) {
		$userInit = AppState::where('name','=','usersInitialized')->get()->first();
		if (! $userInit) {
            $user = new \DemocracyApps\CNP\Entities\Eloquent\User;
            $user->name = "Demo User";
            $user->superuser = false;
            $user->projectcreator=true;
            $user->save();
            \Log::info("Created demo user with id " . $user->getId());
            $person = new \DemocracyApps\CNP\Entities\Person($user->name, $user->getId());
            $person->setContent($user->name);
            $person->save();
            \Log::info("Set person id of Demo User to" . $person->getId());

            $user->elementid = $person->getId();
            $user->save();

			$userInit = new DemocracyApps\CNP\Entities\Eloquent\AppState;
			$userInit->name = 'usersInitialized';
			$userInit->value = '1';
			$userInit->save();			
		}
	}

}

// We need to do some intializations on all the Element types
DemocracyApps\CNP\Entities\CnpComposition::initialize();
DemocracyApps\CNP\Entities\Person::initialize();
DemocracyApps\CNP\Entities\Story::initialize();
DemocracyApps\CNP\Entities\Tag::initialize();
DemocracyApps\CNP\Entities\Organization::initialize();
DemocracyApps\CNP\Entities\StoryElement::initialize();
DemocracyApps\CNP\Entities\Group::initialize();
DemocracyApps\CNP\Entities\Place::initialize();

$mode = Input::get('mode');
if ($mode) {
	Session::put('cnpMode', $mode);
}
else {
	Session::put('cnpMode', 'app');
}


