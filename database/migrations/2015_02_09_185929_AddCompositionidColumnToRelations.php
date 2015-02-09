<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompositionidColumnToRelations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('relations', function(Blueprint $table)
		{
			$table->integer('compositionid')->nullable();
			$table->foreign('compositionid')->references('id')->on('compositions');
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
			$table->dropColumn('compositionid');
		});
	}

}
