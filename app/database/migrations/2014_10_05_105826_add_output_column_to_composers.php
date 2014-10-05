<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOutputColumnToComposers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('composers', function(Blueprint $table)
		{
            $table->integer('output')->nullable();
            $table->foreign('output')->references('id')->on('composers');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('composers', function(Blueprint $table)
		{
			$table->dropColumn('output');
		});
	}

}
