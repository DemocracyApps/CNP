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
            'client_id'     => '330621543771119',
            'client_secret' => 'd3e39c73146d4277bcfbd76deb153f19',
            'scope'         => array(),
        ),
        'Twitter' => array(
            'client_id'     => 'qgOA3IlyHiv6BBqnNUliXZHUj',
            'client_secret' => 'ZrgzEypOSc5MCMLWfvIwWiOp4qnkAblvobAlsoR4kGydB3NZ8V',
            // No scope - oauth1 doesn't need scope
        ),

	)

);