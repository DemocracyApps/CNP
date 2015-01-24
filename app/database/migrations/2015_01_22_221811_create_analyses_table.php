<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalysesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('analyses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('project');
			$table->foreign('project')->references('id')->on('projects');
			$table->text('specification')->nullable();
			$table->text('notes')->nullable();
			$table->timestamp('last')->nullable();
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
		Schema::drop('analyses');
	}

}
