<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUseridToElements extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('elements', function(Blueprint $table)
		{
            $table->integer('userid');
            $table->foreign('userid')->references('id')->on('users');
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
			$table->dropColumn('userid');
		});
	}

}
