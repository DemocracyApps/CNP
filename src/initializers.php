<?php 

use DemocracyApps\CNP\Entities\Eloquent\AppState;

// Assumes that providers.php has already been called.

// Check whether we need any DB initializations


if (Schema::hasTable('app_state')) {

	if (Schema::hasTable('element_types')) {
		$etInit = AppState::where('name', '=', 'elementTypesInitialized')->first();
		if (! $etInit) {
			\Log::info("Initializing element types");
			$cfig=\CNP::getConfiguration();
			DemocracyApps\CNP\Entities\Eloquent\ElementType::initDB($cfig);
			$etInit = new DemocracyApps\CNP\Entities\Eloquent\AppState;
			$etInit->name = 'elementTypesInitialized';
			$etInit->value = '1';
			$etInit->save();
		}
		DemocracyApps\CNP\Entities\Element::initialize();
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
            $person = new \DemocracyApps\CNP\Entities\Element($user->name, \CNP::getElementTypeId("Person"));
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


