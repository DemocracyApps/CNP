<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectorAutoInputsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('collector_auto_inputs', function(Blueprint $table)
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
		Schema::drop('collector_auto_inputs');
	}

}