<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerspectivesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('perspectives', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('type');
			$table->integer('project');
			$table->foreign('project')->references('id')->on('admin');
			$table->text('specification')->nullable();
			$table->text('description')->nullable();
			$table->boolean('requires_analysis')->default(false);
			$table->timestamp('last')->nullable();
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
		Schema::drop('perspectives');
	}

}
