<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompositionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('compositions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('input_composer_id');
			$table->foreign('input_composer_id')->references('id')->on('composers');
			$table->integer('parent')->nullable();
			$table->foreign('parent')->references('id')->on('compositions'); // batch
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
		Schema::drop('compositions');
	}

}
