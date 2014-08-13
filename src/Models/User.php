<?php
namespace DemocracyApps\CNP\Models;

use Illuminate\Auth\UserTrait as UserTrait;
use Illuminate\Auth\UserInterface as UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait as RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface as RemindableInterface;

class User extends \Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
//	protected $hidden = array('password', 'remember_token');

    public function socials()
    {
        return $this->hasMany('Social');
    }
}
