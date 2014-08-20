<?php
namespace DemocracyApps\CNP\Entities\Eloquent;

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

    public function getUserName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDenizenId()
    {
        return $this->denizenid;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
