<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTopColumnToCompositions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('compositions', function(Blueprint $table)
		{
            $table->bigInteger('top')->nullable();
            $table->foreign('top')->references('id')->on('elements');
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
			$table->dropColumn('top');
		});
	}

}
