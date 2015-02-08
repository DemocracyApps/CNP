<?php

namespace DemocracyApps\CNP\Users;


use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];


	public function getApiKey($generate = null)
	{
		if ( ! $this->apikey && $generate) {
			$hiddenValue = 71 * $this->id;
			$this->apikey = rand().'.'.rand().'.92'.$hiddenValue.'37.'.rand();
			$this->save();
		}
		return $this->apikey;
	}

	public function isVerified ()
	{
		return $this->verified;
	}

	public static function checkVerified($userId)
	{
		$verified = false;
		$user = self::find($userId);
		if ($user != null && $user->verified == true) $verified = true;
		return $verified;
	}

	public static function getApiUser($key)
	{
		$kArray = explode('.', $key);
		if (sizeof($kArray) != 4) throw new \Exception("Invalid API key");
		$hiddenId = substr($kArray[2], 2, -2);
		return $hiddenId/71;
	}

	public static function loginByApiKey($key)
	{
		$uid = self::getApiUser($key);
		$user = User::find($uid);
		if ($user) {
			\Auth::login($user);
		}
	}

}
