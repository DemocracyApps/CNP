<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('relation_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->unique();
			$table->string('allowedfrom')->nullable();
			$table->string('allowedto')->nullable();
			$table->integer('inverse')->nullable();
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
		Schema::drop('relation_types');
	}

}
