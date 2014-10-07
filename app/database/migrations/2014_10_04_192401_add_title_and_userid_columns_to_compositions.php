<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleAndUseridColumnsToCompositions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('compositions', function(Blueprint $table)
		{
            $table->integer('userid');
            $table->foreign('userid')->references('id')->on('users');
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
		Schema::table('compositions', function(Blueprint $table)
		{
			$table->dropColumn('userid');
			$table->dropColumn('project');
		});
	}

}
