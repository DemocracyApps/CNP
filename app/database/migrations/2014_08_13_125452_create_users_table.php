<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('name', 255);
            $table->bigInteger('elementid')->unsigned()->unique()->nullable();
            $table->foreign('elementid')->references('id')->on('elements');
            $table->string('email', 255)->nullable()->unique();
			$table->string('password',60)->nullable();
			$table->boolean('superuser');
			$table->boolean('projectcreator');
            $table->string('apikey', 255)->nullable()->unique();
			$table->boolean('verified')->default(false);
			$table->string('remember_token', 100)->nullable();
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
		Schema::drop('users');
	}

}
