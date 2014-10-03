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

    public function getElementId()
    {
        return $this->elementid;
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

    public function getApiKey($generate = null)
    {
        if ( ! $this->apikey && $generate) {
            $hiddenValue = 71 * $this->id;
            $this->apikey = rand().'.'.rand().'.92'.$hiddenValue.'37.'.rand();
            $this->save();
        }
        return $this->apikey;
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
