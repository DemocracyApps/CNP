<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalysisOutputsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('analysis_outputs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('analysis')->nullable();
			$table->foreign('analysis')->references('id')->on('analyses');
			$table->integer('project')->nullable();
			$table->foreign('project')->references('id')->on('projects');
			$table->text('output')->nullable();
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
		Schema::drop('analysis_outputs');
	}

}
