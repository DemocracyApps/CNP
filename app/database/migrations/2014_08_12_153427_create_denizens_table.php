<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDenizensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('denizens', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->integer('scape');
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
        Schema::drop('denizens');
	}

}
