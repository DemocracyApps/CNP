<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElementsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('elements', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->integer('type');
            $table->string('name');
            $table->text('content')->nullable();
            $table->text('properties')->nullable();
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
        Schema::drop('elements');
	}

}
