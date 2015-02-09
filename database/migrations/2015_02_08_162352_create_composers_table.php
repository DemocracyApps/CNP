<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComposersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('composers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('description')->nullable();
			$table->text('specification')->nullable();
			$table->bigInteger('project');
			$table->foreign('project')->references('id')->on('admin');
			$table->integer('dependson')->nullable();
			$table->foreign('dependson')->references('id')->on('composers');
			$table->string('contains')->nullable();
			$table->integer('output')->nullable();
			$table->foreign('output')->references('id')->on('composers');
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
		Schema::drop('composers');
	}

}
