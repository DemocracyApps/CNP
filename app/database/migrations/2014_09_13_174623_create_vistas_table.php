<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVistasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vistas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('description')->nullable();
			$table->string('input_composers');
			$table->integer('output_composer');
			$table->foreign('output_composer')->references('id')->on('composers');
			$table->text('selector')->nullable();
			$table->bigInteger('scape');
			$table->foreign('scape')->references('id')->on('denizens');
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
		Schema::drop('vistas');
	}

}
