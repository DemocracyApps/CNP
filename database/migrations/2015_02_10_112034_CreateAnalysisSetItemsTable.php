<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalysisSetItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('analysis_set_items', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('analysis_set');
			$table->foreign('analysis_set')->references('id')->on('analysis_sets');
			$table->bigInteger('item');
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
		Schema::drop('analysis_set_items');
	}

}
