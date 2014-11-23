<?php 

return array( 
	
	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => 'Session', 

	/**
	 * Consumers
	 */
	'consumers' => array(

		/**
		 * Facebook - https://developers.facebook.com/apps/330621543771119/settings/
		 */
        'Facebook' => array(
            'client_id'     => 'replace-with-FB-client_id',
            'client_secret' => 'replace-with-FB-client_secret',
            'scope'         => array(),
        ),
        'Twitter' => array(
            'client_id'     => 'replace-with-Twitter-client_id',
            'client_secret' => 'replace-with-Twitter-client_secret',
            // No scope - oauth1 doesn't need scope
        ),

	)

);