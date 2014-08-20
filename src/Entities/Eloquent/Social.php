<?php
namespace DemocracyApps\CNP\Entities\Eloquent;

class Social extends \Eloquent {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'socials';

    public function user()
    {
        return $this->belongsTo('User');
    }
}
