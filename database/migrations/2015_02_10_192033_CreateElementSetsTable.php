<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElementSetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('element_sets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid');
			$table->dateTime('expires');
			$table->text('type')->nullable();
			$table->text('setSpecification')->nullable();
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
		Schema::drop('element_sets');
	}

}
