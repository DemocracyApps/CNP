<?php

use Faker\Factory as Faker;

use DemocracyApps\CNP\Entities as DAEntity;

class DenizensTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();
		$types = ["Open", "Closed", "Private"];

		DB::statement('TRUNCATE TABLE denizens RESTART IDENTITY CASCADE');

		foreach(range(1, 10) as $index) {
			$scape = new DAEntity\Scape($faker->text(25));
			$scape->setProperty('access', $faker->randomElement($types));
			$scape->save();
		}

	}
}