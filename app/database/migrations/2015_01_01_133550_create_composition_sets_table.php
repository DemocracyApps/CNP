<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompositionSetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('composition_sets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('type');
			$table->integer('project')->nullable();
			$table->foreign('project')->references('id')->on('projects');
			$table->text('specification')->nullable();
			$table->text('annotations')->nullable();
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
		Schema::drop('composition_sets');
	}

}
