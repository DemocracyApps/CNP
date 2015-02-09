<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('project_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('project');
			$table->foreign('project')->references('id')->on('admin');
			$table->integer('user');
			$table->foreign('user')->references('id')->on('users');
			$table->integer('access'); // Authorization level
			$table->timestamps();
			$table->unique(array('project', 'user'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('project_users');
	}

}
