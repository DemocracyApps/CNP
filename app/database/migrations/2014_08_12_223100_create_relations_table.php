<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('relations', function(Blueprint $table)
		{
			$table->increments('id');
            $table->bigInteger('fromid');
            $table->foreign('fromid')->references('id')->on('denizens');
            $table->bigInteger('toid');
            $table->foreign('toid')->references('id')->on('denizens');
            $table->integer('relationid');
            $table->foreign('relationid')->references('id')->on('relation_types');
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
		Schema::drop('relations');
	}

}
