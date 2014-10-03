<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameScapeColumnOfElements extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('elements', function(Blueprint $table)
		{
			$table->renameColumn('scape', 'project');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('elements', function(Blueprint $table)
		{
			$table->renameColumn('project', 'scape');
		});
	}

}
