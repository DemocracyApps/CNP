<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectColumnToRelations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('relations', function(Blueprint $table)
		{
            $table->integer('project');
            $table->foreign('project')->references('id')->on('projects');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('relations', function(Blueprint $table)
		{
			$table->dropColumn('project');
		});
	}

}
