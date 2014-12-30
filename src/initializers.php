<?php 

use DemocracyApps\CNP\Entities\Eloquent\AppState;

// Assumes that providers.php has already been called.

// Check whether we need any DB initializations


if (Schema::hasTable('app_state')) {
	\Log::info("Here we are!!");
	if (Schema::hasTable('element_types')) {
		$etInit = AppState::where('name', '=', 'elementTypesInitialized')->first();
		\Log::info("Next 1!!");
		if (! $etInit) {
			\Log::info("Initializing element types");
			$cfig=\CNP::getConfiguration();
			DemocracyApps\CNP\Entities\Eloquent\ElementType::initDB($cfig);
			$etInit = new DemocracyApps\CNP\Entities\Eloquent\AppState;
			$etInit->name = 'elementTypesInitialized';
			$etInit->value = '1';
			$etInit->save();
		}
		\Log::info("Next 2!!");
		// We need to do some initializations on all the Element types
		$types = DemocracyApps\CNP\Entities\Eloquent\ElementType::all();
		foreach ($types as $type) {
			$cname = "DemocracyApps\CNP\Entities\\".$type->name;
			\Log::info("Trying " . $type->name);
			if (class_exists($cname)) {
				\Log::info("Class " . $type->name . " does exist.");
			}
		}
		DemocracyApps\CNP\Entities\CnpComposition::initialize();
		DemocracyApps\CNP\Entities\Person::initialize();
		DemocracyApps\CNP\Entities\Story::initialize();
		DemocracyApps\CNP\Entities\Tag::initialize();
		DemocracyApps\CNP\Entities\Organization::initialize();
		DemocracyApps\CNP\Entities\StoryElement::initialize();
		DemocracyApps\CNP\Entities\Group::initialize();
		DemocracyApps\CNP\Entities\Place::initialize();
		DemocracyApps\CNP\Entities\Picture::initialize();

	}


	if (Schema::hasTable('relation_types')) {

		$rtinit = AppState::where('name','=', 'relationTypesInitialized')->first();
		if (! $rtinit) {
			\Log::info("Initializing relationTypes");
			$cfig = \CNP::getConfiguration();
			DemocracyApps\CNP\Entities\Eloquent\RelationType::initDB($cfig);
			$rtinit = new DemocracyApps\CNP\Entities\Eloquent\AppState;
			$rtinit->name = 'relationTypesInitialized';
			$rtinit->value = '1';
			$rtinit->save();
		}
	}

    $picStorage = AppState::where('name', '=', 'pictureStorage')->first();
    if (! $picStorage) {
        \Log::info("Initializing picture storage");
        $picStorage = new DemocracyApps\CNP\Entities\Eloquent\AppState;
        $picStorage->name = 'pictureStorage';
        $picStorage->value = 'S3&cnptest';
        $picStorage->save();
    }

	if (Schema::hasTable('users')) {
		$userInit = AppState::where('name','=','usersInitialized')->first();
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

$mode = Input::get('mode');
if ($mode) {
	Session::put('cnpMode', $mode);
}
else {
	Session::put('cnpMode', 'app');
}


