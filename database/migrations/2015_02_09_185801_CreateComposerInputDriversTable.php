<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComposerInputDriversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('composer_input_drivers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid');
			$table->dateTime('expires');
			$table->text('driver')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('composer_input_drivers');
	}

}
