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
			$table->text('topelements')->nullable();
			$table->integer('specification')->nullable();
			$table->foreign('specification')->references('id')->on('collectors');
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
