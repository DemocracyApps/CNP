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
			$table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('email', 255)->nullable();
            $table->string('facebookid',255)->nullable();
            $table->string('twitterid',255)->nullable();
            $table->string('googleid',255)->nullable();
            $table->string('password',60);
            $table->string('gender', 32)->nullable();
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
